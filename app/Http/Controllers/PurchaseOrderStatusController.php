<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PurchaseOrderStatusController extends Controller
{
    /**
     * Confirm a purchase order.
     */
    public function confirm(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            if (!$purchaseOrder->canBeConfirmed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order cannot be confirmed in its current status.',
                    'current_status' => $purchaseOrder->status,
                ], 400);
            }

            if ($purchaseOrder->confirm()) {
                Log::info("Purchase order {$purchaseOrder->purchase_order_id} confirmed by user {$request->user()->user_id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order confirmed successfully.',
                    'new_status' => $purchaseOrder->status,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm purchase order.',
            ], 500);

        } catch (\Exception $e) {
            Log::error("Error confirming purchase order: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while confirming the purchase order.',
            ], 500);
        }
    }

    /**
     * Mark purchase order as ready for dispatch.
     */
    public function markReadyForDispatch(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            if (!$purchaseOrder->canBeReadyForDispatch()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order cannot be marked as ready for dispatch in its current status.',
                    'current_status' => $purchaseOrder->status,
                ], 400);
            }

            if ($purchaseOrder->markAsReadyForDispatch()) {
                Log::info("Purchase order {$purchaseOrder->purchase_order_id} marked as ready for dispatch by user {$request->user()->user_id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order marked as ready for dispatch.',
                    'new_status' => $purchaseOrder->status,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark purchase order as ready for dispatch.',
            ], 500);

        } catch (\Exception $e) {
            Log::error("Error marking purchase order as ready for dispatch: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the purchase order status.',
            ], 500);
        }
    }

    /**
     * Mark purchase order as dispatched.
     */
    public function markDispatched(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            if (!$purchaseOrder->canBeDispatched()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order cannot be marked as dispatched in its current status.',
                    'current_status' => $purchaseOrder->status,
                ], 400);
            }

            if ($purchaseOrder->markAsDispatched()) {
                Log::info("Purchase order {$purchaseOrder->purchase_order_id} marked as dispatched by user {$request->user()->user_id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order marked as dispatched.',
                    'new_status' => $purchaseOrder->status,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark purchase order as dispatched.',
            ], 500);

        } catch (\Exception $e) {
            Log::error("Error marking purchase order as dispatched: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the purchase order status.',
            ], 500);
        }
    }

    /**
     * Cancel a purchase order.
     */
    public function cancel(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            // Only allow cancellation of draft or confirmed orders
            if (!in_array($purchaseOrder->status, ['draft', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order cannot be cancelled in its current status.',
                    'current_status' => $purchaseOrder->status,
                ], 400);
            }

            $purchaseOrder->status = 'cancelled';
            if ($purchaseOrder->save()) {
                Log::info("Purchase order {$purchaseOrder->purchase_order_id} cancelled by user {$request->user()->user_id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order cancelled successfully.',
                    'new_status' => $purchaseOrder->status,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel purchase order.',
            ], 500);

        } catch (\Exception $e) {
            Log::error("Error cancelling purchase order: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling the purchase order.',
            ], 500);
        }
    }

    /**
     * Get available status transitions for a purchase order.
     */
    public function getAvailableTransitions(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $transitions = [];

        switch ($purchaseOrder->status) {
            case 'draft':
                $transitions = ['confirm', 'cancel'];
                break;
            case 'confirmed':
                $transitions = ['mark_ready_for_dispatch', 'cancel'];
                break;
            case 'ready_for_dispatch':
                $transitions = ['mark_dispatched'];
                break;
            case 'dispatched':
                $transitions = []; // Only inventory receipt can change status
                break;
            case 'partially_received':
            case 'fully_received':
                $transitions = []; // These are set by inventory receipt
                break;
            case 'cancelled':
                $transitions = []; // No transitions from cancelled
                break;
        }

        return response()->json([
            'success' => true,
            'current_status' => $purchaseOrder->status,
            'available_transitions' => $transitions,
            'can_receive_payments' => $purchaseOrder->canReceivePayments(),
        ]);
    }
} 