<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['key', 'value', 'type', 'is_editable'];

    public static function core()
    {
        return static::where('type', 'core');
    }

    public static function custom()
    {
        return static::where('type', 'custom');
    }
}
