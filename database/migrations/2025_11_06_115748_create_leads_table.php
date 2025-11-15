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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->float('estimated_value');
            $table->enum('status',['new','on_going', 'completed', 'lost'])->default('new');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('pipeline_stage_id')
                ->constrained('pipeline_stages')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('lost_reason_id')
                ->nullable()
                ->constrained('lost_reasons')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->enum('interest_levels', ['Frio', 'Morno', 'Quente'])->default('Morno');
            $table->date('closed_at')->nullable();
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
        Schema::dropIfExists('leads');
        Schema::enableForeignKeyConstraints();
    }
};
