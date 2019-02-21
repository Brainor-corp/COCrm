<?php
/**
 * Created by PhpStorm.
 * User: Brainor-PC-000001
 * Date: 24.01.2019
 * Time: 14:13
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller

{
    public function changeUserPassword(Request $request){
        $user = User::where('id', $request->user_id)->first();
        if($user && ($request->password === $request->repeat_password)){
            $user->password = Hash::make($request->password);
            $user->save();
        }
        else{
            throw new \Exception('error');
        }
        return "Пароль был успешно изменен.";
    }
}