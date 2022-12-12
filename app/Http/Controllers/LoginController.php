<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse;
use Auth;

class LoginController extends BaseController
{
    /**
     * Get login page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!empty(Auth::check())){
            return redirect()->route('dashboard');
        }
        $title = "Login";
    	return view('auth/login',['title' => $title]);
    }

    /**
     * Manage Login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postManageLogin(Request $request)
    {
        $response = new ServiceResponse;
        $reqData = $request->all();
        if($reqData){
            $remember = ($request->input('remember')) ? true : false;
            $userData = Auth::attempt([ 'phone_number' => $reqData['email'], 'password' => $reqData['password'],'user_type_id' => 1],$remember);
            $id = Auth::user();
            if (!empty($userData)){
                $response->redirectURL = redirect()->intended('user/dashboard')->getTargetUrl();
                $response->IsSuccess = true;
                $response->Message = "Login has been successfully.";
                
            }else{
                $response->Message = "You have entered incorrect phone_number or password";
            }
        }else{
            $response->Message =  trans('messages.ERR100');
        }
        return $this->GetJsonResponse($response);
    }
}
