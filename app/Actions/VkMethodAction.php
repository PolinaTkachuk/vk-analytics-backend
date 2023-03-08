<?php
 namespace App\Actions;

 use App\Actions\VkDataProcessingAction;
 use App\Models\Group;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Http;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Arr;
 use Carbon\Carbon;

 //инизиализируем вк методы и получ ответ в формате json(если данных много) или значением, отправляем в VkDataProcessing
 class VkMethodAction{

     public function getAvatarDescriptionAndstatusGroup(Request $request)
     {
         $request_url=$request->request->get('url');

         $group_id_object=Group::where('url', $request_url)->get('group_id');
         $group_id=$group_id_object[0]->group_id;

         $group_name_object=Group::where('url', $request_url)->get('name');
         $group_name=$group_name_object[0]->name;

         $response = Http::get(config('services.vk.method').'groups.getById',[
             'group_ids' => $group_name,
             'group_id' => $group_id,
             'fields' => 'description,status',
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);

         $response_data = json_decode($response, true);
         $response_ = $response_data['response'][0];

         $status = array("status" =>  $response_['status']);
         $avatar = array("avatar" =>  $response_['photo_200']);
         $description = array("description" => $response_['description']);
         $url = array("url" => $request_url);

         $response_json = array_merge($url, $description, $status, $avatar);
         return $response_json;
     }
     public function getNameUrlAndIdGroup(Request $request)
     {
         #ПРОВЕРКА НА корректность введеной ссылки!!!!!!!!!!!!!url иначе невероя имя вычисл и тд
         $request_url=$request->request->get('url');// url
         $group_name = trim(parse_url($request_url, PHP_URL_PATH), "/");// name из url

         $response = Http::get(config('services.vk.method').'utils.resolveScreenName',[
             'screen_name' => $group_name,
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);
         //передаем url и name так как это обязательные поля при создании модели
         $response_data = json_decode($response, true);
         $id=$response_data["response"];
         $url=array("url" => $request_url);
         $name=array("name" => $group_name);

         $response_json = array_merge($id, $url, $name);
         //dd($response_json);
         return $response_json;
     }


     public function getInfoMembersGroup(Request $request){

         $request_url=$request->request->get('url');

         //dd($request_url);
         //Надо обязательно сначало заполнить группу
         //А потом уже идти в этот метод получать статистику
         $group_id_object=Group::where('url', $request_url)->get('group_id');
         $group_id=$group_id_object[0]->group_id;

         $response = Http::get(config('services.vk.method').'groups.getMembers',[
             'group_id' => $group_id,
             'fields' => 'relation,bdate,sex,online,city',
             'offset' => 100,
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);
         $response_data = json_decode($response, true);

         $items=$response_data["response"]["items"];
         $count_of_member = array("count_of_member" => $response_data["response"]["count"]); //Общее кол-во участников

         //dd($items);
         $number_of_sex=array("sex_men" => 0,"sex_women" => 0);
         $online=array("online" => 0);
         $relation=array("not_married_1" => 0, "meet_2" => 0, "engaged_3" => 0, "married_4" => 0,
             "its_complicated_5" => 0, "active_search_6" =>0, "in_love_7" => 0,
             "in_a_civil_marriage_8" => 0, "not_exhibited" => 0, );//по категориям отношения, для диаграммы

         $age=array(); //массив возрастов подписчиков
         $general_age=0;//суммарный возраст подписчиков
         $middle_age=array("middle_age" => 0);//средний возраст
         $age_categories=array("under_18" => 0, "19_35" => 0, "36_45" => 0, "46_55" => 0, "56_70" => 0, "over_70" => 0);// возрастные категории
         //текущая дата
         $date = Carbon::now()->format('Y');//format('Y-m-d');


         //Цикл с offset пока не конец items пока не конец count
         //может быть не указыны данные(пол,сп) или удаленные аккаунты, тогда не учитываем их
         foreach ($items as $items_element){
             if($items_element["online"]==1)  $online["online"]+=1;
             if($items_element["sex"]==1) $number_of_sex["sex_women"]+=1;
             if($items_element["sex"]==2) $number_of_sex["sex_men"]+=1;

             if(array_key_exists('relation', $items_element) )
                     switch ($items_element['relation']){
                         case 1: $relation["not_married_1"]+=1;
                         case 2: $relation["meet_2"]+=1;
                         case 3: $relation["engaged_3"]+=1;
                         case 4: $relation["married_4"]+=1;
                         case 5: $relation["its_complicated_5"]+=1;
                         case 6: $relation["active_search_6"]+=1;
                         case 7: $relation["in_love_7"]+=1;
                         case 8: $relation["in_a_civil_marriage_8"]+=1;
                         case 0: $relation["not_exhibited"]+=1;
                     }

             //else {$relation["not_exhibited"]+=1;}
             //$items_element["sex"]==1==1 ? $number_of_men["sex_men"]+=1

             //рассматриваем даты только с указанием года. И вычисл возраст только вычитая года
             //определяем формат даты рождения -вхождение 2х точек 01.01.2001
             // ВВЕСТИ ОГРАНИЧЕНИЕ? на возраст год не больше 1923, чтоб отмести выбросы-по типу 122года
             if(array_key_exists('bdate', $items_element ) &&
                 substr_count($items_element["bdate"], '.')==2) {
                 $year=explode(".", $items_element["bdate"])[2];
                 array_push($age,(int)$date-(int)$year);
                 //dd($age);
             }

         }
         dd($relation);
         //dd($age);
         //считаем средний возраст и категории
         foreach ($age as $age_element) {
             $general_age+=$age_element;

             //dd(gettype($age_element));
             switch ($age_element){

                 case $age_element<19: $age_categories["under_18"]+=1;
                 case 19<=$age_element && $age_element<35: $age_categories["19_35"]+=1;
                 case 36<=$age_element && $age_element<45: $age_categories["36_45"]+=1;
                 case 46<=$age_element && $age_element<55:  dd($age_element);//$age_categories["46_55"]+=1;
                 case (56<=$age_element and $age_element<70): dd($age_element);//$age_categories["56_70"]+=1;
                 case $age_element>=70: $age_categories["over_70"]+=1;

             }
         }
         //dd($age_categories);
         //dd($general_age);

         //После цикла считаем средний возраст делим на всех участников
         $middle_age["middle_age"]=$general_age/$count_of_member["count_of_member"];
         //dd($middle_age);

         //цикл пока не конец items считываем данные в массивы пол возраст онлайн в отнош+-
         //offset =последний items смещаем и пока не count

     }


 }

