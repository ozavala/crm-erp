<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntryLine extends Model
{
    use HasFactory;

    protected $primaryKey = 'journal_entry_line_id';

    protected $fillable = [
        'journal_entry_id',
        'account_name', // Will be 'account_id' later
        'debit_amount',
        'credit_amount',
        'entity_id',
        'entity_type',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id', 'journal_entry_id');
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}