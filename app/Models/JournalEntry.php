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
        'entry_date',
        'transaction_type',
        'description',
        'referenceable_id',
        'referenceable_type',
        'created_by_user_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id', 'journal_entry_id');
    }

    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'created_by_user_id', 'user_id');
    }
}