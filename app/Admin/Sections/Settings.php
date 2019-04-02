<?php

namespace App\Admin\Sections;

use App\Contact;
use App\Equipment;
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
use Illuminate\View\View;
use Illuminate\Http\Request;


class Settings extends Section
{
    protected $title = 'Настройки';

    public static function onDisplay(Request $request){
        $display = Display::table([
            Column::text('name', 'Название'),
            Column::text('value', 'Значение'),
        ])->setPagination(10);

        return $display;
    }

    public static function onCreate()
    {
        return self::onEdit();
    }

    public static function onEdit()
    {
        $form = Form::panel([
            FormColumn::column([
                FormField::input('name', 'Название')->setRequired(true),
                FormField::input('value', 'Значение')->setRequired(true),
            ])
        ]);

        return $form;
    }

    public function isDeletable() {
        return false;
    }

    public function isCreatable() {
        return false;
    }

}