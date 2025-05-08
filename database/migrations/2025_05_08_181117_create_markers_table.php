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
        Schema::create('markers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nimi võib olla ka tühi
            $table->decimal('latitude', 10, 7); // Täpsus: 10 numbrit kokku, 7 kohta peale koma
            $table->decimal('longitude', 10, 7); // Täpsus: 10 numbrit kokku, 7 kohta peale koma
            $table->text('description')->nullable();
            // 'added' ja 'edited' jaoks kasutame Laravel standardseid timestamps
            $table->timestamps(); // See loob created_at (added) ja updated_at (edited)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markers');
    }
};