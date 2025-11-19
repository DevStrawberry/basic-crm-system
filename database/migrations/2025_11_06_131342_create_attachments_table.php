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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('related_table');
            $table->unsignedBigInteger('related_id');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->string('filename');
            $table->string('file_path');
            $table->string('content_type');
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
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
        Schema::dropIfExists('attachments');
        Schema::enableForeignKeyConstraints();
    }
};
