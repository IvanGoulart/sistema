<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('financial_entries', function (Blueprint $table) {
      $table->id();

      $table->foreignId('tenant_id')
        ->constrained()
        ->cascadeOnDelete();

      $table->foreignId('category_id')
        ->nullable()
        ->constrained('financial_categories')
        ->nullOnDelete();

      $table->foreignId('schedule_id')
        ->nullable()
        ->constrained('schedules')
        ->nullOnDelete();

      $table->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      $table->enum('type', ['income', 'expense']);

      $table->string('description');

      $table->decimal('amount', 10, 2);

      $table->date('due_date')->nullable();

      $table->timestamp('paid_at')->nullable();

      $table->enum('status', ['pending', 'paid', 'canceled'])
        ->default('pending');

      $table->string('payment_method')->nullable();

      $table->timestamps();

      $table->index(['tenant_id', 'type']);
      $table->index(['tenant_id', 'status']);
      $table->index(['tenant_id', 'due_date']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('financial_entries');
  }
};
