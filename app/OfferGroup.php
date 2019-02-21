<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Model;

class OfferGroup extends Model
{
    use Uuids;
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class,'id', 'user_id');
    }

    public function offers(){
        return $this->hasMany(Offer::class, 'group_id', 'id');
    }

    public function equipment(){
        return $this->belongsToMany(Equipment::class)->withPivot('quantity');
    }

    public function getTotalWorkPrice(){
        $adjustment = $this->adjusters_number * $this->adjustment_days * $this->adjusters_wage + $this->adjustment_days * $this->fuel_number;
        $noTaxProfit = $this->adjusters_no_tax ?? (($adjustment * (100 - $this->pay_percentage)) / $this->pay_percentage) + $adjustment;

        $taxes = Setting::where('class', 'tax')->get();
        $value = function($item) {
            return intval($item->value);
        };
        $taxMaxPercentage = $taxes->max($value);
        return round($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100), 2);
    }

    public function getTotalWorkPriceNoVAT(){
        $adjustment = $this->adjusters_number * $this->adjustment_days * $this->adjusters_wage + $this->adjustment_days * $this->fuel_number;
        $noTaxProfit = $this->adjusters_no_tax ?? (($adjustment * (100 - $this->pay_percentage)) / $this->pay_percentage) + $adjustment;

        return round($noTaxProfit * 1.0638297873);
    }

    public function getAdditionalDiscount(){
        $adjustment = $this->adjusters_number * $this->adjustment_days * $this->adjusters_wage + $this->adjustment_days * $this->fuel_number;
        $noTaxProfit = $this->adjusters_no_tax ?? (($adjustment * (100 - $this->pay_percentage)) / $this->pay_percentage) + $adjustment;

        $taxes = Setting::where('class', 'tax')->get();
        $value = function($item) {
            return intval($item->value);
        };
        $taxMaxPercentage = $taxes->max($value);
        $VAT =  ($noTaxProfit + ($noTaxProfit * $taxMaxPercentage / 100)) * 20 / 120;

        return round(($noTaxProfit + $noTaxProfit * 40 / 100) - round($noTaxProfit * 1.0638297873) - $VAT, 2);
    }

}