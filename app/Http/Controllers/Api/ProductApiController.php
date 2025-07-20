<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'features', 'warehouses']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('low_stock')) {
            $query->whereHas('warehouses', function ($q) {
                $q->whereRaw('quantity <= reorder_point');
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'features', 'warehouses', 'orderItems', 'invoiceItems']);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable|string|in:active,inactive,discontinued',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'reorder_point' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id . '|max:100',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'status' => 'nullable|string|in:active,inactive,discontinued',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'reorder_point' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): JsonResponse
    {
        // Verificar si el producto tiene órdenes o facturas asociadas
        if ($product->orderItems()->exists() || $product->invoiceItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with associated orders or invoices'
            ], 400);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get stock information for a product
     */
    public function getStock(Product $product): JsonResponse
    {
        $stock = $product->warehouses()->with('warehouse')->get();
        
        $totalStock = $stock->sum('quantity');
        $lowStockWarehouses = $stock->where('quantity', '<=', 'reorder_point');

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'total_stock' => $totalStock,
                'stock_by_warehouse' => $stock,
                'low_stock_alerts' => $lowStockWarehouses,
                'reorder_point' => $product->reorder_point,
                'max_stock' => $product->max_stock,
            ]
        ]);
    }

    /**
     * Update stock for a product
     */
    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|string|in:add,subtract,set',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $warehouseId = $request->warehouse_id;
        $quantity = $request->quantity;
        $operation = $request->operation;

        $warehouse = $product->warehouses()->where('warehouse_id', $warehouseId)->first();

        if (!$warehouse) {
            // Crear nueva entrada de stock
            $product->warehouses()->create([
                'warehouse_id' => $warehouseId,
                'quantity' => $operation === 'set' ? $quantity : ($operation === 'add' ? $quantity : 0),
                'reorder_point' => $product->reorder_point ?? 0,
            ]);
        } else {
            // Actualizar stock existente
            $newQuantity = match($operation) {
                'add' => $warehouse->quantity + $quantity,
                'subtract' => max(0, $warehouse->quantity - $quantity),
                'set' => $quantity,
            };

            $warehouse->update(['quantity' => $newQuantity]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $this->getStock($product)->getData()->data
        ]);
    }
} 