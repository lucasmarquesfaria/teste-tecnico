<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pendente', 'em_andamento', 'concluida'])->default('pendente');
            $table->foreignId('client_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('technician_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
            
            // Adicionar índice para ordenação por data de criação
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('service_orders');
    }
};
