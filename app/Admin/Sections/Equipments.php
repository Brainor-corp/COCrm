<?php

namespace App\Admin\Sections;

use App\Contact;
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


class Equipments extends Section
{
    protected $title = 'Оборудование';
//    protected $model = '\App\Equipment';

    public static function onDisplay(Request $request){
        $display = Display::table([
            Column::text('code', 'Артикул'),
            Column::text('name', 'Название'),
            Column::text('description', 'Описание'),
            Column::text('points', 'Ед. измерения'),
            Column::text('price', 'Цена'),
            Column::text('created_at', 'Дата добавления'),
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
                FormField::input('code', 'Артикул')->setRequired(true),
                FormField::input('name', 'Название')->setRequired(true),
                FormField::textarea('description', 'Описание'),
                FormField::input('points', 'Ед. измерения')->setValue('---'),
                FormField::input('price', 'Цена')->setRequired(true),
                FormField::input('price_trade', 'Розница')->setRequired(true),
                FormField::input('price_small_trade', '3 колонка')->setRequired(true),
                FormField::input('price_special', 'Спец. цена')->setRequired(true),
                FormField::hidden('class')->setValue('equipment'),

                FormField::select('type_id', 'Тип')
                    ->setModelForOptions(Type::class)
                    ->setDisplay('name'),
            ])
        ]);

        return $form;
    }

    public function beforeSave(Request $request, $model = null)
    {
        $duplicate = User::where([['email', $request->email],['id', '!=', $request->id]])->first();
        if($duplicate){
            throw  new \Exception("Пользователь с таким адресом электронной почты уже зарегестрирован!");
        }
    }

}