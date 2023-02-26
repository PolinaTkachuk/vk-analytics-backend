<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;


class StoreController extends Controller
{
   public function signup(Request $request){

       //обрабатываем пришедший реквест $request->validate([
       $request->validate([
           'user.login'=> 'required|string',   //required|!!!!!!!!!!!!!!
           'user.email'=> 'required|email',
           'user.password'=> 'required|string'
       ]);

       $user = User::create([
           'name'=>$request->input('user.login'),
           'email'=>$request->input('user.email'),
           'password'=>Hash::make($request->input('user.password'))
       ]);

       return response()->json();
       /*

       $user = new App\Models\User();
       $user->password = Hash::make($data('password'));
       $user->email = $data('email');
       $user->login = $data('email');
       $user->save();
       */


       /*
       //$data = $request->validate();
       $data=$request;
       //хэшируем пароль
       $data['password']= Hash::make($data['password']);
       //проверка на существование пользователей с одинаковыми полями
       User::firstOrCreate([
           'login'=>$data['login'],
           'email'=>$data['email'],
           'password'=>$data['password']
       ], $data);*/
      // return 11111111;
   }
}
