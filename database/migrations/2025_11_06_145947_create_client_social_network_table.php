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
        Schema::create('client_social_network', function (Blueprint $table) {
            $table->primary(['client_id', 'social_network_id']);
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignId('social_network_id')
                ->constrained('social_networks')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('profile_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('client_social_network');
        Schema::enableForeignKeyConstraints();
    }
};
