<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'source_id'];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
