<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('diagnosed_by_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->text('problem_description');
            $table->text('customer_needs');
            $table->text('possible_solutions');
            $table->enum('urgency_level', ['Baixa', 'Média', 'Alta'])->default('Média');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('diagnostics');
        Schema::enableForeignKeyConstraints();
    }
};
