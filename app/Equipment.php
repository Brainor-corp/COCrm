<?php

namespace App;

use App\Http\Helpers\EquipmentHelper;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use Sluggable;

    protected $fillable = [
        'name', 'code', 'description', 'short_description', 'type_id', 'price', 'price_special', 'price_trade', 'price_small_trade', 'points', 'price', 'class'
    ];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function type(){
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    public function sets(){
        return $this->belongsToMany(Set::class, 'equipment_set', 'equipment_id', 'set_id');
    }

    public function offers(){
        return $this->belongsToMany(Offer::class, 'equipment_offer', 'equipment_id', 'offer_id')->withPivot('quantity');
    }

    public function offer_group(){
        return $this->belongsToMany(Equipment::class, 'equipment_offer_group', 'equipment_id', 'offer_group_id')->withPivot('quantity');
    }

    public function types(){
        return $this->belongsToMany(Type::class, 'equipment_type', 'equipment_id', 'type_id')->withPivot('quantity');
    }

    public function getRealClassAttribute() {
        return EquipmentHelper::getRealClassName($this->class);
    }
}
