<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\User;
use Auth, Hash, Validator;

class ProfileController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
    	$title = "Change Password";
    	return view('user/change-password',['title' => $title]);
    }
    
    /**
    * Save update password
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeUpdatePassword(Request $request){
        $data = $request->all();
        $response = new ServiceResponse;
        $check_fields = array('pw_old','pw_new');
        $check_required_field = $this->checkRequestData($check_fields,$data);

        if($check_required_field == 'SUCC100'){
            $record = Auth::user();
            if(Hash::check($data['pw_old'],$record->password)){
                if($data['pw_old'] != $data['pw_new']){
                    $record->password = Hash::make($data['pw_new']);
                    $record->save();
                    Auth::logout();

                    $response->Message = "Password save successfully.";
                    $response->IsSuccess = true;
                }else{
                    $response->Message = "Old password and new password not same";
                }
            }else{
                $response->Message = "Current password not match";
            }
        }
        else{
            $response->Message = $check_required_field;
        }
        return $this->GetJsonResponse($response);
    }
}