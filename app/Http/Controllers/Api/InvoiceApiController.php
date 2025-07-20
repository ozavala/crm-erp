<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceReminder;

class InvoiceApiController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['customer', 'items.product']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
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
            $query->whereDate('invoice_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->get('date_to'));
        }

        if ($request->has('overdue')) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', 'paid');
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $invoices = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ]
        ]);
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['customer', 'items.product', 'payments']);

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'nullable|string|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
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
            $invoiceData = $validator->validated();
            $items = $invoiceData['items'];
            unset($invoiceData['items']);

            // Generar número de factura
            $invoiceData['invoice_number'] = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create($invoiceData);

            // Crear items de la factura
            $totalAmount = 0;
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discount = $item['discount'] ?? 0;
                $itemTotal = $subtotal - $discount;

                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $discount,
                    'total' => $itemTotal,
                ]);

                $totalAmount += $itemTotal;
            }

            // Actualizar totales
            $shippingCost = $invoiceData['shipping_cost'] ?? 0;
            $discountAmount = $invoiceData['discount_amount'] ?? 0;
            $taxAmount = $invoiceData['tax_amount'] ?? 0;

            $invoice->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount + $shippingCost + $taxAmount - $discountAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice->load(['customer', 'items.product'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:invoice_date',
            'status' => 'nullable|string|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
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

        $invoice->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully',
            'data' => $invoice->load(['customer', 'items.product'])
        ]);
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        // Solo permitir eliminar facturas en estado draft
        if ($invoice->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete invoice that is not in draft status'
            ], 400);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Send invoice to customer
     */
    public function sendInvoice(Request $request, Invoice $invoice): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'send_email' => 'boolean',
            'email_template' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar estado a enviado
        $invoice->update(['status' => 'sent']);

        // Enviar email si se solicita
        if ($request->get('send_email', true) && $invoice->customer->email) {
            try {
                Mail::to($invoice->customer->email)
                    ->send(new InvoiceReminder($invoice));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sent successfully via email',
                    'data' => $invoice
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice status updated but email could not be sent',
                    'error' => $e->getMessage(),
                    'data' => $invoice
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice status updated to sent',
            'data' => $invoice
        ]);
    }
} 