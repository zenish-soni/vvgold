<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\User;

class RegisterController extends BaseController
{
    /**
     * Validate Register
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('phone_number');
        $checkRequiredField = $this->checkRequestDataAPI($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $phoneNumber = $reqData['phone_number'];
            $record = User::where("phone_number",$phoneNumber)->first();

            if(!empty($record)){
                $response->Message = "Phone number has been already registered.";
            }else{
                $record = new User;
                $record->name = !empty($reqData['name']) ? $reqData['name'] : NULL;
                $record->state = !empty($reqData['state']) ? $reqData['state'] : NULL;
                $record->city = !empty($reqData['city']) ? $reqData['city'] : NULL;
                $record->address = !empty($reqData['address']) ? $reqData['address'] : NULL;
                $record->company_name  = !empty($reqData['company_name']) ? $reqData['company_name'] : NULL;
                $record->user_type_id = 2;
                $record->phone_number = $phoneNumber;
                $record->image = AppConstant::$defaultImageName;
                $record->api_token = $this->generateApiToken($record->phone_number);
                $record->save();

                $response->IsSuccess = true;
                $response->Data = $this->getLoginResponse($record);
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
