<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderApiController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = PurchaseOrder::with(['supplier', 'items.product']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                      $supplierQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->get('date_to'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $purchaseOrders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $purchaseOrders->items(),
            'pagination' => [
                'current_page' => $purchaseOrders->currentPage(),
                'last_page' => $purchaseOrders->lastPage(),
                'per_page' => $purchaseOrders->perPage(),
                'total' => $purchaseOrders->total(),
            ]
        ]);
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->load(['supplier', 'items.product', 'goodsReceipts']);

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after_or_equal:order_date',
            'status' => 'nullable|string|in:draft,pending,confirmed,ordered,received,cancelled',
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
            $poData = $validator->validated();
            $items = $poData['items'];
            unset($poData['items']);

            // Generar número de orden de compra
            $poData['po_number'] = 'PO-' . date('Ymd') . '-' . str_pad(PurchaseOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $purchaseOrder = PurchaseOrder::create($poData);

            // Crear items de la orden de compra
            $totalAmount = 0;
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discount = $item['discount'] ?? 0;
                $itemTotal = $subtotal - $discount;

                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $discount,
                    'total' => $itemTotal,
                ]);

                $totalAmount += $itemTotal;
            }

            // Actualizar totales
            $shippingCost = $poData['shipping_cost'] ?? 0;
            $discountAmount = $poData['discount_amount'] ?? 0;
            $taxAmount = $poData['tax_amount'] ?? 0;

            $purchaseOrder->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount + $shippingCost + $taxAmount - $discountAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully',
                'data' => $purchaseOrder->load(['supplier', 'items.product'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'order_date' => 'sometimes|required|date',
            'expected_delivery_date' => 'sometimes|required|date|after_or_equal:order_date',
            'status' => 'nullable|string|in:draft,pending,confirmed,ordered,received,cancelled',
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

        $purchaseOrder->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Purchase order updated successfully',
            'data' => $purchaseOrder->load(['supplier', 'items.product'])
        ]);
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        // Solo permitir eliminar órdenes de compra en estado draft
        if ($purchaseOrder->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete purchase order that is not in draft status'
            ], 400);
        }

        $purchaseOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase order deleted successfully'
        ]);
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:draft,pending,confirmed,ordered,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $purchaseOrder->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Purchase order status updated successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Receive stock for purchase order
     */
    public function receiveStock(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receipt_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
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
            $receiptData = $validator->validated();
            $items = $receiptData['items'];
            unset($receiptData['items']);

            // Crear goods receipt
            $receiptData['purchase_order_id'] = $purchaseOrder->id;
            $receiptData['receipt_number'] = 'GR-' . date('Ymd') . '-' . str_pad(GoodsReceipt::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $goodsReceipt = GoodsReceipt::create($receiptData);

            // Crear items del goods receipt y actualizar stock
            foreach ($items as $item) {
                $goodsReceipt->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_received' => $item['quantity_received'],
                    'unit_cost' => $item['unit_cost'] ?? 0,
                ]);

                // Actualizar stock del producto
                $product = Product::find($item['product_id']);
                $warehouse = $product->warehouses()->where('warehouse_id', $receiptData['warehouse_id'])->first();

                if ($warehouse) {
                    $warehouse->update([
                        'quantity' => $warehouse->quantity + $item['quantity_received']
                    ]);
                } else {
                    $product->warehouses()->create([
                        'warehouse_id' => $receiptData['warehouse_id'],
                        'quantity' => $item['quantity_received'],
                        'reorder_point' => $product->reorder_point ?? 0,
                    ]);
                }
            }

            // Actualizar estado de la orden de compra
            $purchaseOrder->update(['status' => 'received']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock received successfully',
                'data' => [
                    'goods_receipt' => $goodsReceipt,
                    'purchase_order' => $purchaseOrder
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error receiving stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 