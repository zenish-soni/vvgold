<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\User;
use Hash;

class LoginController extends BaseController
{

    /**
    * Validate Login
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function postLogin(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('phone_number');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $phoneNumber = $reqData['phone_number'];

            $record = User::where("phone_number",$phoneNumber)->first();
            if(!empty($record)){
                $record->is_approved = 1;
                $record->api_token = $this->generateApiToken($record->phone_number);
                $record->device_token = !empty($reqData['device_token']) ? $reqData['device_token'] : NULL;
                $record->save();
            }else{
                $record = new User;
                $record->user_type_id = 2;
                $record->phone_number = $phoneNumber;
                $record->image = AppConstant::$defaultImageName;
                $record->api_token = $this->generateApiToken($phoneNumber);
                $record->is_approved = 1;
                $record->device_token = !empty($reqData['device_token']) ? $reqData['device_token'] : NULL;
                $record->save();
            }

            $response->Data = $this->getLoginResponse($record);
            $response->IsSuccess = true;
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
