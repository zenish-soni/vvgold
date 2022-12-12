<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\Setting;
use Validator;

class SettingController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
    	$title = 'Setting';
        $setting = Setting::find(1);
    	return view('user/setting',['title' => $title, 'setting' => $setting]);
    }
    
    /**
    * Save update password
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeUpdateSeting(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $check_fields = array('bank_name','bank_account_name','bank_account_number','branch_name','bank_account_ifsc_number');
        $check_required_field = $this->checkRequestData($check_fields,$reqData);

        if($check_required_field == 'SUCC100'){
            $record = Setting::find(1);
            $record->bank_name = $reqData['bank_name'];
            $record->bank_account_name = $reqData['bank_account_name'];
            $record->bank_account_number = $reqData['bank_account_number'];
            $record->branch_name = $reqData['branch_name'];
            $record->bank_account_ifsc_number = $reqData['bank_account_ifsc_number'];
            if(!empty($reqData['bank_photo'])){
                if(!empty($record->bank_photo))
                    AppConstant::deleteImage($record->bank_photo);
                $record->bank_photo = AppConstant::storeImage($reqData['bank_photo']);
            }
            $record->save();
            $response->IsSuccess = true;
            $response->Message = trans('messages.updated_record');
        }
        else{
            $response->Message = $check_required_field;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function slider(){
        $title = "Slider";
        $sliders = [];
        $sliderDetail = Setting::find(1);
        if(!empty($sliderDetail) && !empty($sliderDetail->slider)){
            $sliderDetail = json_decode($sliderDetail->slider,1);
            foreach($sliderDetail as $slider){
                $sliders[] = array('key' => $slider['file_name'], 'value' => AppConstant::getImage($slider['file_name']));
            }
        }

        return view('user/slider',[
            'title'     => $title, 
            'sliders'   => $sliders
        ]);
    }
    
    /**
    * Save Slider
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function storeSlider(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('image');
        $checkRequiredFields = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredFields == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'image' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $setting = Setting::find(1);
                $fileName = AppConstant::storeImage($request->file('image'));

                if(!empty($setting)){
                    if(!empty($setting->slider)){
                        $sliders = json_decode($setting->slider,1);
                        array_push($sliders, array('file_name' => $fileName));
                        $setting->slider = json_encode($sliders);
                    }else{
                        $setting->slider = json_encode([['file_name' => $fileName]]);
                    }
                    $setting->save();
                }
                $response->IsSuccess = true;
                $response->Message = "Slider has been updated successfully.";
            }
        }
        else{
            $response->Message = $checkRequiredFields;
        }
        return $this->GetJsonResponse($response);
    }

    /**
    * Delete Slider
    * Method POST
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function deleteSlider(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('image');
        $checkRequiredFields = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredFields == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'image' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $setting = Setting::find(1);

                if(!empty($setting)){
                    if(!empty($setting->slider)){
                        $image = $reqData['image'];
                        $sliders = json_decode($setting->slider,1);
                        $newSliders = array();

                        foreach($sliders as $sliVal){
                            if($sliVal['file_name'] != $image){
                                array_push($newSliders, array('file_name' => $sliVal['file_name']));
                            }
                        }
                        
                        $setting->slider = json_encode($newSliders);
                        $setting->save();

                        AppConstant::deleteImage($image);
                    }
                }

                $response->IsSuccess = true;
                $response->Message = "Slider has been updated successfully.";
            }
        }
        else{
            $response->Message = $checkRequiredFields;
        }
        return $this->GetJsonResponse($response);
    }
}