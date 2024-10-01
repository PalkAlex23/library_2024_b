<?php

use App\Models\Copy;
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
        Schema::create('copies', function (Blueprint $table) {
            $table->id('copy_id');
            $table->foreignId('book_id')->references('book_id')->on('books');
            // $table->foreignId('user_id')->references('id')->on('users');
            $table->boolean('hardcovered')->default(1);
            $table->year('publication')->default('2000');
            $table->smallInteger('status')->default(0);
            $table->timestamps();
        });

        Copy::create(['book_id'=> 1, 'user_id'=> 1]);
        Copy::create(['book_id'=> 2, 'user_id'=> 2]);
        Copy::create(['book_id'=> 3, 'user_id'=> 3,'publication' => "2024"]);
        Copy::create(['book_id'=> 1, 'user_id'=> 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copies');
    }
};
