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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('related_table');
            $table->unsignedBigInteger('related_id');
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('assigned_to')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('title');
            $table->text('description');
            $table->date('due_date');
            $table->boolean('completed')->default(false);
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
        Schema::dropIfExists('tasks');
        Schema::enableForeignKeyConstraints();
    }
};
