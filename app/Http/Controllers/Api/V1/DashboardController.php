<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\Product;
use Auth, Validator;

class DashboardController extends BaseController
{
    /**
     * Check Apprvoal
     * Method GET
     *
     * @return \Illuminate\Http\Response
     */
    public function getCheckApproval(){
        $response = new ServiceResponse();
        $authDetail = Auth::guard('api')->user();
        
        $response->is_approved = $authDetail->is_approved;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
     * Dashboard Data
     * Method GET
     *
     * @return \Illuminate\Http\Response
     */
    public function getDashboardData(){
        $response = new ServiceResponse();
        
        $sliders = [];
        $setting = \App\Models\Setting::find(1);
        if(!empty($setting) && !empty($setting->slider)){
            $mSlider = json_decode($setting->slider,1);
            foreach($mSlider as $slider){
                array_push($sliders, array('image' => AppConstant::getImage($slider['file_name'])));
            }
        }
        
        $response->search_terms = \App\Models\LuSearchTerm::select('id','name')->get();
        $response->sliders = $sliders;
        $response->bank_name = $setting->bank_name;
        $response->bank_account_name = $setting->bank_account_name;
        $response->bank_account_number = $setting->bank_account_number;
        $response->branch_name = $setting->branch_name;
        $response->bank_account_ifsc_number = $setting->bank_account_ifsc_number;
        $response->bank_photo = !empty($setting->bank_photo) ? AppConstant::getImage($setting->bank_photo) : '';

        $query = Product::with('size')->where('is_popular',1);
        $records = $query->orderByDesc('id')->get()->toArray();

        if(!empty($records)){
            foreach($records as $key => $record){
                $records[$key]['description'] = $this->convertNullToChar($record['description']);
                $records[$key]['lu_sub_category_id'] = $this->convertNullToChar($record['lu_sub_category_id'],1);
                $records[$key]['sub_sub_category_id'] = $this->convertNullToChar($record['sub_sub_category_id'],1);
                $records[$key]['weight'] = !empty($record['weight']) ? (float)($record['weight']) : 0;
                $records[$key]['image'] = AppConstant::getImage($record['image']);
                $records[$key]['size_name'] = !empty($record['size']) ? $record['size']['name'] : '';
                unset($records[$key]['size']);
            }
        }

        $response->Data = $records;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
     * Logout
     * Method GET
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getLogout(Request $request){
        $response = new ServiceResponse();
        $authDetail = Auth::guard('api')->user();
        $authDetail->api_token = NULL;
        $authDetail->save();

        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }


    /**
     * Get Categories
     * Method GET
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategory(){
        $response = new ServiceResponse();
        $records = \App\Models\LuCategory::where('status',1)->withCount('subCategory')->get();
        if(!empty($records)){
            foreach($records as $key => $record){
                unset($records[$key]['created_at'], $records[$key]['updated_at'], $records[$key]['deleted_at']);
            }
        }
        $response->Data = $records;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Sub Categories
     * Method POST
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSubCategory(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $validator = Validator::make($reqData, [
                'id' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $categoryId = $reqData['id'];

                $records = \App\Models\LuSubCategory::withCount('subSubCategory')->where(['lu_category_id' => $categoryId,'status' => 1])->get()->toArray();

                if(!empty($records)){
                    foreach($records as $key => $record){
                        unset($records[$key]['created_at'], $records[$key]['updated_at'], $records[$key]['deleted_at']);
                    }
                }

                $response->Data = $records;
                $response->IsSuccess = true;
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Sub Sub Categories
     * Method POST
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSubSubCategory(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $validator = Validator::make($reqData, [
                'id' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $categoryId = $reqData['id'];

                $response->Data = \App\Models\SubSubCategory::where(['lu_sub_category_id' => $categoryId, 'status' => 1])->select('id','name','image')->get()->toArray();
                $response->IsSuccess = true;
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
