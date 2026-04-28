<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'isbn', 'year', 'available', 'author_id', 'summary'];

    protected $casts = [
        'available' => 'boolean',
        'year' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function isAvailable()
    {
        return $this->available && $this->borrows()->whereNull('returned_at')->count() === 0;
    }
}