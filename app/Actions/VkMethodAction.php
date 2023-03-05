<?php
 namespace App\Actions;

 use App\Actions\VkDataProcessingAction;
 use App\Models\Group;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Http;
 use Illuminate\Support\Facades\DB;

 //инизиализируем вк методы и получ ответ в формате json(если данных много) или значением, отправляем в VkDataProcessing
 class VkMethodAction{

     public function getAvatarDescriptionAndstatusGroup(Request $request, VkDataProcessingAction $VkDataProcessingAction)
     {
         //ававтар, описание, статус
         $request_url=$request->request->get('url');

         //получаем id текущей группы из БД
         $group_id_object=DB::table('groups')->where('url', $request_url)->get('group_id');
         $group_id=$group_id_object[0]->group_id;
         //dd($group_id);

         $group_name_object=DB::table('groups')->where('url', $request_url)->get('name');
         $group_name=$group_name_object[0]->name;
         //dd($group_name);

         $response = Http::get(config('services.vk.method').'groups.getById',[
             'group_ids' => $group_name,
             'group_id' => $group_id,
             'fields' => 'description,status',
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);

         $response_json = json_decode($response);
         //dd($response_json);

         $description = $response_json->response[0]->description;
         $status = $response_json->response[0]->status;
         $avatar = $response_json->response[0]->photo_200;
         //dd($avatar);
         $VkDataProcessingAction->insertAvatarDescriptionAndstatusGroup($request_url, $description, $status, $avatar);
     }
     public function getNameUrlAndIdGroup(Request $request, VkDataProcessingAction $VkDataProcessingAction)
     {

         $request_url=$request->request->get('url');// url
         $group_name = trim(parse_url($request_url, PHP_URL_PATH), "/");// name из url

         $response = Http::get(config('services.vk.method').'utils.resolveScreenName',[
             'screen_name' => $group_name,
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);

         $response_json = json_decode($response);
         $group_id = $response_json->response->object_id; // получаем id группы object_id

         //dd($group_id);
         $VkDataProcessingAction->insertNameUrlAndIdGroup($request_url, $group_id, $group_name);

         // dd($request->request->get('url'));
         //dd($request);
         //dd(trim(parse_url($request, PHP_URL_PATH), "/"));
         //dd($response_json->response->object_id);
     }

     /*
     public function getMembers(Request $request){
         //получаем url  отрпавляем имя группы
         $response = Http::get(config('services.vk.method').'groups.getMembers',[
             //'group_id=' => //вытаскиваем из бд
             'count' => 1000,
             'v' => config('services.vk.version'),
             'fields' => 'online',
             'access_token' => config('services.vk.token'),
         ]);
         $response_json = $response->json();
         $number_of_group_members = $response_json->response->count; //Общее кол-во участников
     }
     */

 }

