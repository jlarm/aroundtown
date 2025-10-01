<?php

declare(strict_types=1);

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
        Schema::create('location_location_category', static function (Blueprint $table): void {
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_category_id')->constrained()->cascadeOnDelete();

            $table->primary(['location_id', 'location_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_location_category');
    }
};
