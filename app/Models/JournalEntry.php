<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'journal_entry_id';

    protected $fillable = [
        'owner_company_id', // This was the missing piece
        'entry_date',
        'description',
        'transaction_type',
        'created_by_user_id',
        'referenceable_id',
        'referenceable_type',
    ];
    
    protected $casts = [
        'entry_date' => 'date',
    ];

    /**
     * Get the lines for the journal entry.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id', 'journal_entry_id');
    }

    /**
     * Get the parent referenceable model (e.g., Transaction).
     */
    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}