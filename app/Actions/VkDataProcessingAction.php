<?php
namespace App\Actions;

//вызываем метод, данные форматируем в нужный формат и вставляем в модель
use App\Models\Group;
use Symfony\Component\HttpFoundation\Request;
use App\Actions\VkMethodAction;
class VkDataProcessingAction {


    public function insertNameUrlAndIdGroup(string $url, int $group_id, string $group_name){
        //dd("TYT");
        //dd($group_name);
        $group = Group::create([
            'group_id'=>$group_id,
            'name' => $group_name,
            'url'=>$url,
        ]);
    }

    public function insertAvatarDescriptionAndstatusGroup(string $request_url, string $description, string $status, string $avatar){
        $group = Group::updateOrCreate([
            ['url' => $request_url],
            ['status' => $status, 'description' => $description, 'avatar' => $avatar]
        ]);
    }
}
