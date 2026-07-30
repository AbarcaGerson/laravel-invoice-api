<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('series', 10);
            $table->unsignedInteger('number');
            $table->string('customer_name');
            $table->string('customer_document', 20);
            $table->decimal('total', 12, 2);
            $table->string('currency', 3)->default('PEN');
            $table->string('status', 30)->default('pending');
            $table->string('storage_path')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['series', 'number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
