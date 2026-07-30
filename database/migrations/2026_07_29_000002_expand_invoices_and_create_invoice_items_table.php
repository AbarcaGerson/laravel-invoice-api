<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropUnique(['series', 'number']);

            $table->renameColumn('total', 'total_amount');
            $table->renameColumn('customer_document', 'customer_document_number');

            $table->string('document_type', 2)->default('01')->after('uuid');
            $table->string('operation_type', 4)->default('0101')->after('document_type');
            $table->date('issue_date')->after('number');
            $table->date('due_date')->nullable()->after('issue_date');
            $table->string('customer_document_type', 2)->default('6')->after('due_date');
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->decimal('taxable_amount', 12, 2)->default(0)->after('currency');
            $table->decimal('igv_amount', 12, 2)->default(0)->after('taxable_amount');

            $table->unique(['document_type', 'series', 'number']);
        });

        Schema::create('invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('line_number');
            $table->string('product_code', 50)->nullable();
            $table->string('description');
            $table->string('unit_code', 10)->default('NIU');
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('taxable_amount', 12, 2);
            $table->decimal('igv_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('tax_affectation_type', 2)->default('10');
            $table->timestamps();

            $table->unique(['invoice_id', 'line_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');

        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropUnique(['document_type', 'series', 'number']);
            $table->renameColumn('total_amount', 'total');
            $table->renameColumn('customer_document_number', 'customer_document');
            $table->dropColumn([
                'document_type',
                'operation_type',
                'issue_date',
                'due_date',
                'customer_document_type',
                'customer_email',
                'taxable_amount',
                'igv_amount',
            ]);
            $table->unique(['series', 'number']);
        });
    }
};
