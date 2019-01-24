<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
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

    public function equipment(){
        return $this->belongsToMany(Equipment::class, 'equipment_set', 'set_id', 'equipment_id')->withPivot('quantity', 'comment');
    }
}
