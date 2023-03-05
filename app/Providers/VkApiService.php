<?php

namespace App;

use Illuminate\Support\Facades\Http;

class ApiVk{

    //для jobs для отслеживания статистики группы за временной интервал
    private function callApi($group_id){

        $response = Http::get(config('services.vk.method'));
    }


}
