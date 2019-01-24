<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
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

    public function equipments(){
        return $this->belongsTo(Equipment::class);
    }

    public function contacts(){
        return $this->belongsTo(Contact::class);
    }
}
