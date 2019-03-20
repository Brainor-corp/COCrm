<?php

namespace App;

use App\Http\Helpers\EquipmentHelper;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Type extends Model
{
    use Sluggable;

    protected $fillable = [
        'name', 'class'
    ];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function equipment(){
        return $this->belongsTo(Equipment::class);
    }

//    public function equipment(){
//        return $this->belongsToMany(Equipment::class)->withPivot('quantity', 'price', 'price_trade', 'price_small_trade', 'price_special');
//    }

//    public function work(){
//        return $this->belongsToMany(Equipment::class, 'equipment_type', 'type_id', 'equipment_id')->withPivot('quantity');
//    }

    public function contacts(){
        return $this->belongsTo(Contact::class);
    }

    public function getNameClassAttribute() {
        return $this->name . ' (' . EquipmentHelper::getRealClassName($this->class) . ')';
    }
}
