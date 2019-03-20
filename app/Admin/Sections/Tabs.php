<?php

namespace App\Admin\Sections;

use App\Contact;
use App\DefaultTab;
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
use Bradmin\SectionBuilder\Meta\Meta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;


class Tabs extends Section
{
    protected $title = 'Вкладки по умолчанию';
    protected $model = '\App\DefaultTab';

    public static function onDisplay(Request $request)
    {
        $display = Display::table([
            Column::text('name', 'Название'),
            Column::text('real_class', 'Тип вкладки'),
            Column::text('real_display', 'Выводить'),
        ])->setPagination(10);

        return $display;
    }

    public static function onCreate()
    {
        return self::onEdit(null);
    }

    public static function onEdit($id)
    {
        $meta = new Meta;
        $meta->setScripts([
            'body' => [
                asset('packages/select2/js/select2.min.js'),
                asset('js/admin.js')
            ]
        ])->setStyles([
            'select2' => asset('packages/select2/css/select2.min.css')
        ]);

        $tab = DefaultTab::where('id', $id)->with('equipments')->first();

        $form = Form::panel([
            FormColumn::column([
                FormField::input('name', 'Название')->setRequired(true),
                FormField::select('display', 'Выводить по умолчанию')
                    ->setOptions([false=>'Нет', true=>'Да'])
                    ->setRequired(true),
                FormField::select('class', 'Класс')
                    ->setOptions(['equipment'=>'Оборудование', 'work'=>'Работа'])
                    ->setRequired(true),
                FormField::custom(View::make('admin.equipmentAjaxMultiselect')->with(compact('tab')))
            ])
        ])->setMeta($meta);

        return $form;
    }

    public function afterSave(Request $request, $model = null)
    {
        $tab = DefaultTab::where('id', $model->id)->first();
        $tab->equipments()->sync($request->equipment);
    }

    public function beforeSave(Request $request, $model = null)
    {
        if(!$request->has('equipment')) {
            $model->equipments()->sync(null);
        }
    }

    public function beforeDelete(Request $request, $id = null)
    {
        $tab = DefaultTab::where('id', $id)->first();
        $tab->equipments()->detach();
    }
}