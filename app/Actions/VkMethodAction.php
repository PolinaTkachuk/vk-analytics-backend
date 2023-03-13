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

     public function getAvatarDescriptionAndstatusGroup(Request $request): array
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
     public function getNameUrlAndIdGroup(Request $request): array
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

     public function getItemsWithOffsetMembersGroup(Request $request, int $offset): array
     {
         //получаем часть подписчиков с группы, смещение будем менять в getInfoMembersGroup для получ всех подписчиков
         $request_url = $request->request->get('url');

         //dd($request_url);
         //Надо обязательно сначало заполнить группу А потом уже идти в этот метод получать статистику
         $group_id_object = Group::where('url', $request_url)->get('group_id');
         $group_id = $group_id_object[0]->group_id;

         //dd($group_id);
         $response = Http::get(config('services.vk.method') . 'groups.getMembers', [
             'group_id' => $group_id,
             'fields' => 'relation,bdate,sex,online,city',
             'offset' => $offset,
             'access_token' => config('services.vk.token'),
             'v' => config('services.vk.version'),
         ]);
         $response_data = json_decode($response, true);
         return $response_data;
     }

     public function getInfoMembersGroup(Request $request, array $response_data): array
     {

         //dd($response_data);
         $items=$response_data["response"]["items"];
         $count_of_member = array("count_of_member" => $response_data["response"]["count"]); //Общее кол-во участников

         //dd(count($items));// длинна рассматриваемого количества выборки подписчиков без offset
         $number_of_sex=array("sex_men" => 0,"sex_women" => 0);
         $number_of_online=array("online" => 0);
         $relation=array("not_married_1" => 0, "meet_2" => 0, "engaged_3" => 0, "married_4" => 0,
             "its_complicated_5" => 0, "active_search_6" =>0, "in_love_7" => 0,
             "in_a_civil_marriage_8" => 0, "not_exhibited" => 0, );//по категориям отношения, для диаграммы

         $age=array(); //текущий массив возрастов подписчиков (обновляем с каждым обновл items подписчиков)
         $general_age=0;//суммарный возраст подписчиков
         $middle_age=array("middle_age" => 0);//средний возраст
         $age_categories=array("under_18" => 0, "19_35" => 0, "36_45" => 0, "46_55" => 0, "56_70" => 0, "over_70" => 0);// возрастные категории
         $date = Carbon::now()->format('Y');//format('Y-m-d'); //текущая дата

         $city=array(array("title" => "","id" => null), array("count_members_for_the_city" => 0,"id" => null));
         $city_all=[array("title" => ""), array("count_members_for_the_city" => 0)];


         $offset=0;//смещение-число просмотренных подписчиков из суммарного числа
         //пока не проанализировали всех участников группы
         while ($offset < $count_of_member["count_of_member"]) {

             $age=array(); //текущий массив возрастов подписчиков (обновляем с каждым обновл items подписчиков)
             //может быть не указыны данные(пол,сп) или удаленные аккаунты, тогда не учитываем их
             foreach ($items as $items_element) {
                 if ($items_element["online"] == 1) $number_of_online["online"] += 1;
                 if ($items_element["sex"] == 1) $number_of_sex["sex_women"] += 1;
                 if ($items_element["sex"] == 2) $number_of_sex["sex_men"] += 1;

                 if (array_key_exists('relation', $items_element)) {
                     switch ($items_element['relation']) {
                         case 1:
                             $relation["not_married_1"] += 1;
                             break;
                         case 2:
                             $relation["meet_2"] += 1;
                             break;
                         case 3:
                             $relation["engaged_3"] += 1;
                             break;
                         case 4:
                             $relation["married_4"] += 1;
                             break;
                         case 5:
                             $relation["its_complicated_5"] += 1;
                             break;
                         case 6:
                             $relation["active_search_6"] += 1;
                             break;
                         case 7:
                             $relation["in_love_7"] += 1;
                             break;
                         case 8:
                             $relation["in_a_civil_marriage_8"] += 1;
                             break;
                         case 0:
                             $relation["not_exhibited"] += 1;
                             break;
                     }
                 }

                 //рассматриваем даты только с указанием года. И вычисл возраст только вычитая года
                 //определяем формат даты рождения -вхождение 2х точек 01.01.2001
                 // ВВЕСТИ ОГРАНИЧЕНИЕ? на возраст год не больше 1923, чтоб отмести выбросы-по типу 122года
                 if (array_key_exists('bdate', $items_element) && substr_count($items_element["bdate"], '.') == 2) {
                     $year = explode(".", $items_element["bdate"])[2];
                     array_push($age, (int)$date - (int)$year);
                 }

                 //ГОРОДА

                 /*
                 $city_all=[array("title" => "", "count_members_for_the_city" => 0), array("id" => null)];

                 $city_all[0]["title"]=1;
                 dd($city_all);
                 if (array_key_exists('city', $items_element)) {// если у подписчика указан город

                     if (in_array($items_element["city"]['title'], $city_all["title"])) { // если город с таким title уже сохранен в массиве нашем
                         $city_all["count_members_for_the_city"]["title"]+=1;// для города с этим названием +1
                         //array_push($city[1], $city[1]["count_members_for_the_city"]+=1); // увеличиваем количество людей, в нем проживающих
                     } else { // иначе заносим новый город в массив


                         array_push($city_all, $city_all["title"]=$items_element["city"]['title']);
                         $i=$i+1;
                         /*
                         $city[0]["id"] = $items_element["city"]['id'];
                         $city[1]["id"] = $items_element["city"]['id'];
                         $city[1]["count_members_for_the_city"]+=1;

                     }
                 }
                 */

             }

             //считаем средний возраст и категории
             foreach ($age as $age_element) {
                 $general_age += $age_element;

                 switch ($age_element) {
                     case in_array($age_element,  range(0, 19)):
                         $age_categories["under_18"] += 1;
                         break;
                     case in_array($age_element,  range(19, 36)):
                         $age_categories["19_35"] += 1;
                         break;
                     case in_array($age_element,  range(36, 46)):
                         $age_categories["36_45"] += 1;
                         break;
                     case in_array($age_element,  range(46, 56)):
                         $age_categories["46_55"] += 1;
                         break;
                     case in_array($age_element,  range(56, 71)):
                         $age_categories["56_70"] += 1;
                         break;
                     case $age_element >= 70:
                         $age_categories["over_70"] += 1;
                         break;

                 }

             }

             //dd($age_categories);
             //После цикла считаем средний возраст, делим на всех участников
             $middle_age["middle_age"] = $general_age / $count_of_member["count_of_member"];

             $offset = $offset+count($items);//столько подписчиков группы просмотрели
             //смещаем на оставшихся непросмотренных подп-ов
             if ($offset >= $count_of_member["count_of_member"]){

                 $dif = $count_of_member["count_of_member"] - $offset;
                 $offset= $offset + $dif; //сдвигаем не на item а на хвостик

             } else {

                 $response_data = VkMethodAction::getItemsWithOffsetMembersGroup($request, $offset);
                 $items = $response_data["response"]["items"];//рассматриваем новых items подписчиков группы

             }

         }

         //dd($offset);
         //dd($relation);
         //dd($age_categories);
         //dd($number_of_sex);
         //dd($number_of_online);

     }


 }

