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
use Bradmin\SectionBuilder\Filter\Types\BaseType\FilterType;
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
            Column::text('real_class', 'Категория'),
            Column::text('name', 'Название'),
            Column::text('description', 'Описание'),
            Column::text('points', 'Ед. измерения'),
            Column::text('price', 'Цена'),
            Column::text('created_at', 'Дата добавления'),
        ])->setPagination(10)->setFilter([
            FilterType::text('code'),
            FilterType::select('class')->setOptions([
                'equipment' => 'Оборудование',
                'work' => 'Работы'
            ]),
            FilterType::text('name'),
            null,
            null,
            null,
            null,
        ]);

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
                FormField::input('points', 'Ед. измерения')->setRequired(true),
                FormField::input('price', 'Цена')->setRequired(true),
                FormField::input('price_trade', 'Розница')->setRequired(true),
                FormField::input('price_small_trade', '3 колонка')->setRequired(true),
                FormField::input('price_special', 'Спец. цена')->setRequired(true),
                FormField::hidden('class')->setValue('e'),

                FormField::select('type_id', 'Тип')
                    ->setModelForOptions(Type::class)
                    ->setDisplay('name_class'),
            ])
        ]);

        return $form;
    }

    public function afterSave(Request $request, $model = null)
    {
        $equipment = Equipment::where('id', $model->id)->first();
        $equipment->class = Type::where('id', $equipment->type_id)->first()->class;
        $equipment->save();
    }

    public function beforeDelete(Request $request, $id = null)
    {
        $equipment = Equipment::where('id', $id)->first();
        $equipment->offers()->detach();
        $equipment->offer_group()->detach();
        $equipment->types()->detach();
    }

}