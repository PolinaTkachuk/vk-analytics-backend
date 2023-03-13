<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTGuard;
use App\Actions\VkMethodAction;
use App\Actions\VkDataProcessingAction;

class VkGroupController extends Controller
{
    public function mainInfoAboutGroups(Request $request,vkMethodAction $vkMethodAction,VkDataProcessingAction $VkDataProcessingAction)
    {

        $resp=$vkMethodAction->getNameUrlAndIdGroup($request);
        $VkDataProcessingAction->insertNameUrlAndIdGroup($resp);
        $resp_2=$vkMethodAction->getAvatarDescriptionAndstatusGroup($request);
        $VkDataProcessingAction->insertAvatarDescriptionAndstatusGroup($resp_2);

        $resp_3=$vkMethodAction->getItemsWithOffsetMembersGroup($request, 0);

        $resp_4=$vkMethodAction->getInfoMembersGroup($request, $resp_3);
        return response()->json(new GroupResource( (Group::where('url',$request->url)->get())[0] ));

        //$id=Group::where('email',$request->user()->email)->get('id')->toArray();
        //$res = Group::where('url',$request->url)->get();
        //dd($res[0]);
        //dd(Group::find(1));

        // возвращаем ответы только через ресурсы, одна моделька - один класс ресурса для нее
        // имеем класс $class, который создает модель через какой-то метод, пусть он называется createModel. Он должен вернуть созданную модель. Этот контроллер должен быть одной строчкой:
        // return response()->json(new GroupResource($class->createModel$request->input('url'))));
        // метод должен либо возвращать результат, либо вносить изменения(почитай стандарты PSR-12)


        //в классе, который дергает методы мы делаем return response_json , возвращ массив json_decode($resp,true)
        //в классе, который создает модель, вызываем методы возвращающие данные и созд модель через new Group ... сохр и возвращаем групп проверяем на Группа уже в бд существует
        //в классе ресурсы формируем ответ в нужном формате в аргументе- моделька


        //$resp=$vkMethodAction->getNameUrlAndIdGroup($request);
        //$VkDataProcessingAction->insertNameUrlAndIdGroup($resp);
        //$resp_2=$vkMethodAction->getAvatarDescriptionAndstatusGroup($request);
        //$VkDataProcessingAction->insertAvatarDescriptionAndstatusGroup($resp_2);



        //названия функций соответствуют их работе get-возвращает insert- вставляет данные
        //не использую DB:: фасад, все через модель Group::
        //контроллер в строчку


//        $request_url=$request->request->get('url');
//        $response=json_decode(Group::all()->where('url',$request_url));
//        $data = collect($response);
//        $flattened=$data->flatten();
//        $result = $flattened->all();
//        #ПОВТОР КОД (из-за необходимости возвращения в формате не вложенного json)!!!!!!!!!ИСПРАВЬ
//
//        if (Group::where('url',$request_url)->exists()) {//Группа уже в бд существует
//            #dd('1');
//            #$f=$result[0];
//            #dd($f->avatar);
//            return ($result[0]);
//        }
//        else {
//            #dd('2');
//            $vkMethodAction->getNameUrlAndIdGroup($request, $VkDataProcessingAction);
//            $vkMethodAction->getAvatarDescriptionAndstatusGroup($request, $VkDataProcessingAction);
//
//            $response=json_decode(Group::all()->where('url',$request_url));
//            $data = collect($response);
//            $flattened=$data->flatten();
//            $result = $flattened->all();
//            return ($result[0]);
//            #return $response;
//        }
    }
}
