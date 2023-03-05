<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
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
    public function mainInfoAboutGroups(Request $request, VkMethodAction $vkMethodAction,VkDataProcessingAction $VkDataProcessingAction)
    {
        $vkMethodAction->getNameUrlAndIdGroup($request, $VkDataProcessingAction);
        $vkMethodAction->getAvatarDescriptionAndstatusGroup($request, $VkDataProcessingAction);

        //VkMethodAction::class
        //$this->VkMethodAction->getIdGroup($request);
    }
}
