<?php
namespace App\Actions;

//вызываем метод, данные форматируем в нужный формат и вставляем в модель
use App\Models\Group;
use Symfony\Component\HttpFoundation\Request;
use App\Actions\VkMethodAction;
class VkDataProcessingAction {


    public function insertNameUrlAndIdGroup(array $response_json){

        if (!Group::where('url',$response_json['url'])->exists()) {
            $group = new Group([
                'group_id' => $response_json['object_id'],
                'name' => $response_json['name'],
                'url' => $response_json['url']
            ]);
            $group->save();
        }

        //$group=Group::all()->where('url',$response_json['url']);
        //return $group;
    }

    public function insertAvatarDescriptionAndstatusGroup(array $response_json){

        $group = Group::where('url',$response_json['url'])
            ->update([
                'status' => $response_json['status'],
                'description'=> $response_json['description'],
                'avatar' => $response_json['avatar'],
            ]);

        //$group=Group::all()->where('url',$response_json['url']);
        //return $group;
    }
}
