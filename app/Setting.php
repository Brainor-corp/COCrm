<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $fillable = [
        'name', 'value', 'class'
    ];
}
