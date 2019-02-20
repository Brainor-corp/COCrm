<?php

namespace App\Admin\Sections;

use Bradmin\Section;
use Bradmin\SectionBuilder\Display\BaseDisplay\Display;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;


class ExcelUploads extends Section
{
    protected $title = 'Excel загрузка';
    protected $model = '\App\ExcelUpload';

    public static function onDisplay(Request $request){
        $display = Display::custom([])
            ->setView(View::make('admin.excelUpload'));

        return $display;
    }

}