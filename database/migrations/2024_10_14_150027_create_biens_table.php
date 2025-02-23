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
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('imagePath')->nullable();
            $table->string('titre');
            $table->text('description');
            $table->decimal('prix', 10, 2);
            $table->boolean('disponible')->default(true);
            $table->integer('nombre_douches')->nullable();
            $table->integer('nombre_chambres')->nullable();
            $table->decimal('superficie', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
