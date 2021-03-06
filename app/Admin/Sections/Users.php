<?php

namespace App\Admin\Sections;

use App\Post;
use App\User;
use Bradmin\Section;
use Bradmin\SectionBuilder\Display\BaseDisplay\Display;
use Bradmin\SectionBuilder\Display\Table\Columns\BaseColumn\Column;
use Bradmin\SectionBuilder\Form\BaseForm\Form;
use Bradmin\SectionBuilder\Form\Panel\Columns\BaseColumn\FormColumn;
use Bradmin\SectionBuilder\Form\Panel\Fields\BaseField\FormField;
use Bradmin\SectionBuilder\Meta\Meta;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class Users extends Section
{
    protected $title = 'Пользователи';

    public static function onDisplay(){
        $display = Display::table([
            Column::text('id', '#'),
            Column::text('name', 'Имя'),
            Column::text('email', 'EMail'),
            Column::text('phone_number', 'Телефон'),
            Column::text('contact_email', 'Контактная почта'),
        ])->setPagination(10);

        return $display;
    }

    public static function onCreate()
    {
        $form = Form::panel([
            FormColumn::column([
                FormField::input('name', 'Имя')->setRequired(true),
                FormField::input('last', 'Фамилия')->setRequired(true),
                FormField::input('middle', 'Отчество')->setRequired(true),
                FormField::input('company', 'Компания')->setRequired(true),
                FormField::input('position', 'Должность')->setRequired(true),
                FormField::hidden('verified')->setValue(0),
                FormField::input('email', 'EMail')->setRequired(true)
                    ->setType('email'),
                FormField::input('contact_email', 'Контактная почта')->setRequired(true)
                    ->setType('email'),
                FormField::input('phone_number', 'Телефон'),
                FormField::input('mobile_number', 'Моб. телефон'),
                FormField::input('password', 'Пароль')
                    ->setRequired(true)
                    ->setType('password'),
                FormField::input('repeat_password', 'Повторите пароль')
                    ->setRequired(true)
                    ->setType('password'),
            ]),
        ]);

        return $form;
    }

    public static function onEdit(Request $request)
    {
        $meta = new Meta();
        $meta->setScripts([
            'body' => [
                asset('js/adminUser.js')
            ]
        ]);
        $user_id = $request->id;
        $form = Form::panel([
            FormColumn::column([
                FormField::input('name', 'Имя')->setRequired(true),
                FormField::input('last', 'Фамилия')->setRequired(true),
                FormField::input('middle', 'Отчество')->setRequired(true),
                FormField::input('company', 'Компания')->setRequired(true),
                FormField::input('position', 'Должность')->setRequired(true),
                FormField::input('email', 'EMail')->setRequired(true),
                FormField::input('contact_email', 'Контактная почта')->setRequired(true)
                    ->setType('email'),
                FormField::input('phone_number', 'Телефон'),
                FormField::input('mobile_number', 'Моб. телефон'),
                FormField::custom(View::make('admin.userPasswordChange')->with(compact('user_id')))
            ]),
        ])
            ->setMeta($meta);

        return $form;
    }

    public function beforeSave(Request $request, $model = null)
    {
        if($request->password !== $request->repeat_password){
            throw  new \Exception("Пароли не совпадают, попытайтесь снова");
        }

        $duplicate = User::where([['email', $request->email],['id', '!=', $request->id]])->first();
        if($duplicate){
            throw  new \Exception("Пользователь с таким адресом электронной почты уже зарегестрирован!");
        }
    }

    public function afterSave(Request $request, $model = null)
    {
        if($request->has('password')){
            $model->password = Hash::make($request->password);
            $model->save();
        }
    }

}