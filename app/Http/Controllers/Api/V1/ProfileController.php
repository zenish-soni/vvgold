<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController, App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\User;
use Auth;

class ProfileController extends BaseController
{

    /**
     * Store Profile
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProfile(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;

        $authDetail = Auth::guard('api')->user();
        $authDetail->name = !empty($reqData['name']) ? $reqData['name'] : NULL;
        $authDetail->state  = !empty($reqData['state']) ? $reqData['state'] : NULL;
        $authDetail->city  = !empty($reqData['city']) ? $reqData['city'] : NULL;
        $authDetail->address  = !empty($reqData['address']) ? $reqData['address'] : NULL;
        $authDetail->company_name  = !empty($reqData['company_name']) ? $reqData['company_name'] : NULL;
        if(!empty($reqData['business_photo'])){
            $authDetail->business_photo = AppConstant::storeImage($reqData['business_photo']);
        }

        if(!empty($reqData['second_business_photo'])){
            $authDetail->second_business_photo = AppConstant::storeImage($reqData['second_business_photo']);
        }
        $authDetail->save();

        $response->Data = $this->getLoginResponse($authDetail);
        unset($response->Data['api_token']);

        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);        
    }
}
