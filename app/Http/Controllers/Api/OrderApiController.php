<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['customer', 'items.product']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'items.product', 'payments']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'nullable|string|in:draft,pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $orderData = $validator->validated();
            $items = $orderData['items'];
            unset($orderData['items']);

            // Generar número de orden
            $orderData['order_number'] = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create($orderData);

            // Crear items de la orden
            $totalAmount = 0;
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discount = $item['discount'] ?? 0;
                $itemTotal = $subtotal - $discount;

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $discount,
                    'total' => $itemTotal,
                ]);

                $totalAmount += $itemTotal;
            }

            // Actualizar totales
            $shippingCost = $orderData['shipping_cost'] ?? 0;
            $discountAmount = $orderData['discount_amount'] ?? 0;
            $taxAmount = $orderData['tax_amount'] ?? 0;

            $order->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount + $shippingCost + $taxAmount - $discountAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load(['customer', 'items.product'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'order_date' => 'sometimes|required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'nullable|string|in:draft,pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order->load(['customer', 'items.product'])
        ]);
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order): JsonResponse
    {
        // Solo permitir eliminar órdenes en estado draft
        if ($order->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete order that is not in draft status'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:draft,pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldStatus = $order->status;
        $order->update($validator->validated());

        // Si la orden se confirma, verificar stock
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $insufficientStock = [];
            
            foreach ($order->items as $item) {
                $product = $item->product;
                $availableStock = $product->warehouses->sum('quantity');
                
                if ($availableStock < $item->quantity) {
                    $insufficientStock[] = [
                        'product' => $product->name,
                        'required' => $item->quantity,
                        'available' => $availableStock
                    ];
                }
            }

            if (!empty($insufficientStock)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock for some products',
                    'insufficient_stock' => $insufficientStock
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
} 