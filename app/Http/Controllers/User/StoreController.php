<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTGuard;


class StoreController extends Controller
{
   public function signup(Request $request){

       //обрабатываем пришедший реквест $request->validate([
       $request->validate([
           'user.login'=> 'required|string',
           'user.email'=> 'required|email',
           'user.password'=> 'required|string'
       ]);

       $user = User::create([
           'name'=>$request->input('user.login'),
           'email'=>$request->input('user.email'),
           'password'=>Hash::make($request->input('user.password'))
       ]);

       //Get a token based on a given user's id.
       $token = auth()->tokenById($user->id);
       return response(['access_token'=> $token, 'id'=>$user->id]);



       //$token = auth()->login($user);
       //$token = auth()->user();
       //return auth('api')->attempt($user);
       //return response()->json();
       /*
       //$data = $request->validate();
       $data=$request;
       //хэшируем пароль
       //проверка на существование пользователей с одинаковыми полями
       User::firstOrCreate([
           'login'=>$data['login'],
           'email'=>$data['email'],
           'password'=>$data['password']
       ], $data);*/
      // return 11111111;
   }


    public function login(Request $request)
    {
       // dd(DB::select('show create table users'), User::find(1));
        $credentials=[
           'email'=>$request->input('user.email'),
            'password'=>$request->input('user.password')];
        //$credentials = request(['user.name']);

        // Генерируем токен для пользователя, если учетные данные действительны
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       // /** @var JWTGuard $token **/
        $guard = auth('api');
        $token = $guard->attempt($credentials);

        // Get some user from somewhere
        //$user = User::find(87);
        // Get the token
       // $token_old = auth()->login($user);

        //dd($token_old);
        //dd($token);
        //dd(DB::select('show create table users'),  DB::table('users')->find(88));
        if(!$token){
            $token = auth()->refresh();
            return $token;//('Token NOT provided!');
        }

        $id=DB::table('users')->where('email',$request->user()->email)->get('id')->toArray();
        //dd($id[0]->id);
        return $this->respondWithToken($token, $id[0]->id);
    }


    public function saveProfile(Request $request){

       return 1;
    }

    /*
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }
    */

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        dd('11111111111');
        //нужно добавить id в метод respondWithToken
        return $this->respondWithToken(auth()->refresh());
    }


    protected function respondWithToken($token, $id)
    {
        return response()->json([
            'access_token' => $token,
            'id' => $id,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
