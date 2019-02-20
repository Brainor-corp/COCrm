<?php

namespace App\Admin\Sections;

use App\Contact;
use App\Equipment;
use App\Offer;
use App\OfferGroup;
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
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;


class OfferGroups extends Section
{
    protected $title = 'Группа КП';
    protected $model = '\App\OfferGroup';

    public static function onDisplay(Request $request){

        $display = Display::table([
            Column::text('name', 'Наименование')->setSortable(true),
            Column::text('uuid', 'Уникальный ID'),
            Column::text('template', 'Является шаблоном'),
        ])
            ->setPagination(10)
        ->setFilter([FilterType::text('name', '-'), null, null]);

        return $display;
    }

    public function isCreatable()
    {
        return false;
    }

    public static function onEdit($model){
        $modelData = OfferGroup::where('id', $model)->first();
        $form = Form::panel([
            FormColumn::column([
                FormField::input('name', 'Наименовение'),
                FormField::datepicker('created_at', 'Время создания')
                    ->setFormat('yyyy-mm-dd hh:ii:00')
                    ->setRequired(true)
                    ->setLanguage('ru')
                    ->setTodayBtn(true)
                    ->setClearBtn(true),
                FormField::select('template', 'Является шаблоном')
                    ->setOptions([0=>'Нет', 1=>'Да'])
                    ->setRequired(true),
                FormField::custom(View::make('admin.kpSection')->with(compact('model','modelData')))
            ])
        ]);

        return $form;
    }

    public function beforeDelete(Request $request, $id = null)
    {
        $deleteOfferGroup = OfferGroup::where('id', $id)->first();
        Offer::where('group_id', $deleteOfferGroup->id)->delete();
    }

}