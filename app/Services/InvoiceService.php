<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    private const IGV_RATE = 0.18;

    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data): Invoice {
            $items = $this->buildItems($data['items']);
            $totals = $this->calculateTotals($items);

            $invoice = Invoice::query()->create([
                'uuid' => (string) Str::uuid(),
                'document_type' => $data['document_type'],
                'operation_type' => $data['operation_type'] ?? '0101',
                'series' => strtoupper($data['series']),
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'customer_document_type' => $data['customer_document_type'],
                'customer_document_number' => $data['customer_document_number'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'currency' => strtoupper($data['currency'] ?? 'PEN'),
                'taxable_amount' => $totals['taxable_amount'],
                'igv_amount' => $totals['igv_amount'],
                'total_amount' => $totals['total_amount'],
                'status' => Invoice::STATUS_PENDING,
            ]);

            $invoice->items()->createMany($items);

            return $invoice->load('items');
        });
    }

    private function buildItems(array $items): array
    {
        return array_map(function (array $item, int $index): array {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $taxableAmount = round($quantity * $unitPrice, 2);
            $igvAmount = round($taxableAmount * self::IGV_RATE, 2);

            return [
                'line_number' => $index + 1,
                'product_code' => $item['product_code'] ?? null,
                'description' => $item['description'],
                'unit_code' => strtoupper($item['unit_code'] ?? 'NIU'),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'taxable_amount' => $taxableAmount,
                'igv_amount' => $igvAmount,
                'total_amount' => round($taxableAmount + $igvAmount, 2),
                'tax_affectation_type' => $item['tax_affectation_type'] ?? '10',
            ];
        }, $items, array_keys($items));
    }

    private function calculateTotals(array $items): array
    {
        return [
            'taxable_amount' => round(array_sum(array_column($items, 'taxable_amount')), 2),
            'igv_amount' => round(array_sum(array_column($items, 'igv_amount')), 2),
            'total_amount' => round(array_sum(array_column($items, 'total_amount')), 2),
        ];
    }
}
