<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Author;
use App\Models\Book;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Jean Dupont', 'email' => 'jean@example.com', 'password' => Hash::make('password123'), 'subscription_status' => 'free']);
        User::create(['name' => 'Marie Martin', 'email' => 'marie@example.com', 'password' => Hash::make('password123'), 'subscription_status' => 'premium']);
        User::create(['name' => 'Pierre Durand', 'email' => 'pierre@example.com', 'password' => Hash::make('password123'), 'subscription_status' => 'free']);

        Author::create(['first_name' => 'Victor', 'last_name' => 'Hugo', 'nationality' => 'Française']);
        Author::create(['first_name' => 'George', 'last_name' => 'Orwell', 'nationality' => 'Britannique']);
        Author::create(['first_name' => 'Jane', 'last_name' => 'Austen', 'nationality' => 'Britannique']);

        Book::create(['title' => 'Les Misérables', 'isbn' => '9780451525260', 'year' => 1862, 'author_id' => 1]);
        Book::create(['title' => '1984', 'isbn' => '9780451524935', 'year' => 1949, 'author_id' => 2]);
        Book::create(['title' => 'Orgueil et Préjugés', 'isbn' => '9780141439518', 'year' => 1813, 'author_id' => 3]);
    }
}