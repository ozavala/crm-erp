<?php

namespace App\Models;

enum StatusEnum: string
{
    // Purchase Order Statuses
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case READY_FOR_DISPATCH = 'ready_for_dispatch';
    case DISPATCHED = 'dispatched';
    case PARTIALLY_RECEIVED = 'partially_received';
    case FULLY_RECEIVED = 'fully_received';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    // Order Statuses
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    // Invoice Statuses
    case SENT = 'sent';
    case OVERDUE = 'overdue';

    // Bill Statuses
    case AWAITING_PAYMENT = 'awaiting_payment';

    // Quotation Statuses
    case DRAFT_QUOTATION = 'draft';
    case SENT_QUOTATION = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    // Task Statuses
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';

    // Goods Receipt Statuses
    case RECEIVED = 'received';

    public function getDisplayName(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::CONFIRMED => 'Confirmed',
            self::READY_FOR_DISPATCH => 'Ready for Dispatch',
            self::DISPATCHED => 'Dispatched',
            self::PARTIALLY_RECEIVED => 'Partially Received',
            self::FULLY_RECEIVED => 'Fully Received',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::SENT => 'Sent',
            self::OVERDUE => 'Overdue',
            self::AWAITING_PAYMENT => 'Awaiting Payment',
            self::SENT_QUOTATION => 'Sent',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::EXPIRED => 'Expired',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On Hold',
            self::RECEIVED => 'Received',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::DRAFT, self::PENDING => 'gray',
            self::CONFIRMED, self::PROCESSING, self::IN_PROGRESS => 'blue',
            self::READY_FOR_DISPATCH, self::SENT, self::SENT_QUOTATION => 'yellow',
            self::DISPATCHED, self::SHIPPED => 'orange',
            self::PARTIALLY_RECEIVED, self::PARTIALLY_PAID => 'purple',
            self::FULLY_RECEIVED, self::DELIVERED, self::COMPLETED, self::PAID => 'green',
            self::CANCELLED, self::REJECTED, self::EXPIRED => 'red',
            self::OVERDUE => 'red',
            self::AWAITING_PAYMENT => 'yellow',
            self::ACCEPTED => 'green',
            self::ON_HOLD => 'orange',
            self::RECEIVED => 'green',
        };
    }

    public static function getPurchaseOrderStatuses(): array
    {
        return [
            self::DRAFT,
            self::CONFIRMED,
            self::READY_FOR_DISPATCH,
            self::DISPATCHED,
            self::PARTIALLY_RECEIVED,
            self::FULLY_RECEIVED,
            self::PARTIALLY_PAID,
            self::PAID,
            self::CANCELLED,
        ];
    }

    public static function getOrderStatuses(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::SHIPPED,
            self::DELIVERED,
            self::PARTIALLY_PAID,
            self::PAID,
            self::CANCELLED,
        ];
    }

    public static function getInvoiceStatuses(): array
    {
        return [
            self::DRAFT,
            self::SENT,
            self::PARTIALLY_PAID,
            self::PAID,
            self::OVERDUE,
            self::CANCELLED,
        ];
    }

    public static function getQuotationStatuses(): array
    {
        return [
            self::DRAFT_QUOTATION,
            self::SENT_QUOTATION,
            self::ACCEPTED,
            self::REJECTED,
            self::EXPIRED,
        ];
    }
} 