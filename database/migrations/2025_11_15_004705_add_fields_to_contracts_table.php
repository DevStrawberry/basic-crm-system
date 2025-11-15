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
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('status', ['Em elaboração', 'Aguardando Assinatura', 'Assinado', 'Fechado', 'Cancelado'])->default('Em elaboração')->after('contract_number');
            $table->foreignId('assigned_to')
                ->nullable()
                ->after('proposal_id')
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('restrict');
            $table->date('deadline')->nullable()->after('final_value');
            $table->text('notes')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['status', 'assigned_to', 'deadline', 'notes']);
        });
    }
};
