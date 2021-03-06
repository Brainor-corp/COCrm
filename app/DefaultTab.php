<?php

namespace App;

use App\Http\Helpers\EquipmentHelper;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class DefaultTab extends Model
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
        'name', 'class', 'display'
    ];

    public function equipments(){
        return $this->belongsToMany(Equipment::class);
    }

    public function getRealClassAttribute(){
        return EquipmentHelper::getRealClassName($this->class);
    }

    public function getRealDisplayAttribute(){
        return EquipmentHelper::getRealDisplayValues($this->display);
    }
}
