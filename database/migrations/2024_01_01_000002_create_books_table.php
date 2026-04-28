<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->integer('year');
            $table->boolean('available')->default(true);
            $table->text('summary')->nullable();
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('books'); }
};