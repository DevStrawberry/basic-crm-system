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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('proposal_id')
                ->constrained('proposals')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('contract_number');
            $table->float('final_value');
            $table->float('payment_method');
            $table->string('signed_by')->nullable();
            $table->date('signed_at')->nullable();
            $table->string('file_path')->nullable();
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
        Schema::dropIfExists('contracts');
        Schema::enableForeignKeyConstraints();
    }
};
