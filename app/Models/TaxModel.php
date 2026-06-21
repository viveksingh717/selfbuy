<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxModel extends Model
{
    use HasFactory;

    protected $table = 'tax_models';
    protected $fillable = [
        'tax_name',
        'tax_alias',
        'tax_type',
        'tax_rate',
        'applicable_to',
        'tax_region',
        'priority',
        'description',
        'status',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
    ];

    // ── Accessors ──

    public function getIsActiveAttribute(): bool
    {
        return $this->status == 1;
    }

    public function getFormattedRateAttribute(): string
    {
        return $this->tax_type === 'fixed'
            ? '₹' . number_format($this->tax_rate, 2)
            : $this->tax_rate . '%';
    }
}
