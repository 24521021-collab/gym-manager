<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
<<<<<<< Updated upstream
    //
}
=======
    protected $fillable = [
        'category', 'title', 'slug', 'header_image', 'content', 'author_id', 'status'
    ];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
}
>>>>>>> Stashed changes
