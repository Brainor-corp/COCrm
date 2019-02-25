<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 19.02.2019
 * Time: 16:42
 */

namespace App\Http\Helpers;


class CurrenciesHelper
{
    public static function getCurrenciesValues() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, "https://www.cbr-xml-daily.ru/daily_json.js");
            $result=curl_exec($ch);
            curl_close($ch);

            return json_decode($result, true);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getCurrencyFromString($str) {
        $result = "РУБ";

        $currencies = [
            "РУБ" => "РУБ",
            "RUB" => "РУБ",
            "USD" => "USD",
            "У.Е." => "USD",
            "EUR" => "EUR"
        ];

        foreach ($currencies as $key => $currency) {
            if(preg_match('/'.$key.'/ui', $str)) {
                return $currency;
            }
        }

        return $result;
    }
}