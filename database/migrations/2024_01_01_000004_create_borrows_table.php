<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamp('borrowed_at');
            $table->timestamp('returned_at')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'free'])->default('pending');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('borrows'); }
};