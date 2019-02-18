<?php

namespace App\Admin\Sections;

use App\Contact;
use App\Equipment;
use App\OfferGroup;
use App\Role;
use App\Type;
use App\User;
use Bradmin\Section;
use Bradmin\SectionBuilder\Display\BaseDisplay\Display;
use Bradmin\SectionBuilder\Display\Table\Columns\BaseColumn\Column;
use Bradmin\SectionBuilder\Display\Table\DisplayTable;
use Bradmin\SectionBuilder\Form\BaseForm\Form;
use Bradmin\SectionBuilder\Form\Panel\Columns\BaseColumn\FormColumn;
use Bradmin\SectionBuilder\Form\Panel\Fields\BaseField\FormField;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;


class OfferGroups extends Section
{
    protected $title = 'Группа КП';
//    protected $model = '\App\Equipment';

    public static function onDisplay(Request $request){
        $offerGroups = OfferGroup::all();
        $display = Display::custom([])->setView(View::make('admin.kpSection')->with(compact('offerGroups')));

        return $display;
    }

    public static function onCreate()
    {
        return self::onEdit();
    }

    public static function onEdit()
    {
        return redirect(url('google.com'));
    }

}