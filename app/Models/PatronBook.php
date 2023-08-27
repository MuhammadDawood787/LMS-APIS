<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
class PatronBook extends Model
{
    use HasFactory;
    protected $fillable = ['returned_at'];
    protected $table = 'patron_books';
    public $timestamps = true;
}
