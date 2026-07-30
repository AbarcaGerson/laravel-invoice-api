<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'uuid',
        'document_type',
        'operation_type',
        'series',
        'number',
        'issue_date',
        'due_date',
        'customer_document_type',
        'customer_document_number',
        'customer_name',
        'customer_email',
        'currency',
        'taxable_amount',
        'igv_amount',
        'total_amount',
        'status',
        'storage_path',
        'processing_error',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'issue_date' => 'date',
            'due_date' => 'date',
            'taxable_amount' => 'decimal:2',
            'igv_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
