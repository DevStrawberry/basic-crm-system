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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->enum('type', ['Ligação', 'E-mail', 'Reunião', 'Mensagem', 'Nota'])->default('Nota');
            $table->string('subject');
            $table->text('body');
            $table->date('happened_at')->default(now());
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
        Schema::dropIfExists('interactions');
        Schema::enableForeignKeyConstraints();
    }
};
