<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['pivot'];

    public function posts() {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
