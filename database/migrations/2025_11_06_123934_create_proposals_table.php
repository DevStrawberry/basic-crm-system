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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('title');
            $table->text('body');
            $table->float('total_value');
            $table->date('valid_until');
            $table->enum('status', ['Draft', 'Enviada', 'Aceita', 'Rejeitada'])->default('Draft');
            $table->date('sent_at')->nullable();
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
        Schema::dropIfExists('proposals');
        Schema::enableForeignKeyConstraints();
    }
};
