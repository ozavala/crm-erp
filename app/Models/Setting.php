<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'type',
        'is_editable'];

    public static function core()
    {
        return static::where('type', 'core');
    }

    public static function custom()
    {
        return static::where('type', 'custom');
    }
    protected static function booted(): void
    {
        static::deleting(function (Setting $setting) {
            // Prevenir la eliminación de settings 'core'
            if ($setting->type === 'core') {
                return false; // Esto hace que el método delete() retorne false
            }
        });
    }

    /**
     * Scope a query to only include core settings.
     */
    public function scopeCore(Builder $query): Builder
    {
        return $query->where('type', 'core');
    }

    /**
     * Scope a query to only include custom settings.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('type', 'custom');
    }
}
