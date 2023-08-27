<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patron extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'address'];

    public function borrowedBooks()
    {
        return $this->belongsToMany(Book::class, 'patron_books')
            ->withPivot(['borrowed_at', 'due_date', 'returned_at']);
    }
}
