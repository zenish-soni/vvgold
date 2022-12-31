<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\PageModel, App\Infrastructure\AppConstant;
use App\Models\SubSubCategory, App\Models\LuCategory;
use Validator, DB;

class SubSubCategoryController extends BaseController
{
   	/**
     * Display view
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = LuCategory::with('subCategory')->get();
        $records = route('sub_sub_categories.list');
        $storeRecord = route('sub_sub_category.store');
        $destoryRecord = route('sub_sub_category.delete');
        $changeStatus = route('sub_sub_category.change_status');
        $title = "Sub Sub Category";

        return view('user/sub-sub-image-types',[
            'title' => $title,
            'records' => $records, 
            'storeRecord' => $storeRecord, 
            'destoryRecord' => $destoryRecord,
            'changeStatus' => $changeStatus,
            'categories' => $categories
        ]);
    }

    /**
     * Display the list resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxList(Request $request)
    {
        $response = new ServiceResponse();
        $reqData = $request->all();
        $pageIndex = $reqData['Data']['PageIndex'];
        $pageSize = $reqData['Data']['PageSize'];
        $limit = $pageSize;
        $offset = ($pageIndex - 1) * $pageSize;
        
        $query = SubSubCategory::whereHas('category')->whereHas('subCategory')->with('category','subCategory');

        if(array_key_exists('name',$reqData['Data']['SearchParams']) && !empty(trim($reqData['Data']['SearchParams']['name'])) && (trim($reqData['Data']['SearchParams']['name']) != "") ) {
            $name =  trim($reqData['Data']['SearchParams']['name']);
            $query = $query->where('name','LIKE','%'.$name.'%');
        }

        if(array_key_exists('parent_id',$reqData['Data']['SearchParams']) && !empty(trim($reqData['Data']['SearchParams']['parent_id'])) && (trim($reqData['Data']['SearchParams']['parent_id']) != "") ) {
            $parentID =  trim($reqData['Data']['SearchParams']['parent_id']);
            $query = $query->where($typeData['parent_id'],$parentID);
        }

        $countQuery = $query;
        $countQueryCount = $countQuery->count();
        $records = $query->orderByDesc('id')->take($limit)->offset($offset)->get()->toArray();

        if (count($records)>0) {
            $index = ($pageSize * ($pageIndex-1)) + 1;
            foreach ($records as $key => $item) {
                $records[$key]['Index'] = $index++;
                $records[$key]['category_name'] = $item['category']['name'];
                $records[$key]['sub_category_name'] = $item['sub_category']['name'];
            }
        }

        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = $countQueryCount;
        $pageModel->ItemsPerPage = $pageSize;
        $pageModel->TotalPages = ceil($pageModel->TotalItems / $pageModel->ItemsPerPage);
        $pageModel->Items = $records;

        $response->Data = $pageModel;
        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('lu_category_id','lu_sub_category_id','name');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $tableName = SubSubCategory::getTableName();
            $categoryId = $reqData['lu_category_id'];
            $subCategoryId = $reqData['lu_sub_category_id'];
            
            $validator = Validator::make($reqData, [
                'name' => 'required|unique:'.$tableName.',name,'.$id.',id,lu_category_id,'.$categoryId.',lu_sub_category_id,'.$subCategoryId
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $updated_at = $this->getDateTime();
                
                $record = $id > 0 ? SubSubCategory::find($id) : new SubSubCategory;
                $record->lu_category_id = $reqData['lu_category_id'];
                $record->lu_sub_category_id = $reqData['lu_sub_category_id'];
                $record->name = $reqData['name'];
                if($request->file('file')){
                    if($id > 0){
                        AppConstant::deleteImage($record->image);
                    }
                    $record->image = AppConstant::storeImage($request->file('file'));
                }
                $record->save();

                $response->IsSuccess = true;
                $response->Message =  $id > 0 ? trans('messages.updated_record') : trans('messages.added_record');
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $tableName = SubSubCategory::getTableName();
            $record = SubSubCategory::find($id);

            if(!empty($record)){
                $name = $id.'_'.$record->name;
                DB::update('update '.$tableName.' set name=? where id = ?',array($name,$id));
                $record->delete();

                $response->IsSuccess = true;
                $response->Message =  trans('messages.deleted_record');
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Change user status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $record = SubSubCategory::find($id);
            $record->status = !empty($reqData['checkedValue']) ? $reqData['checkedValue'] : 0;
            $record->save();

            $response->IsSuccess = true;
            $response->Message = $record->status == 0 ?  "Item has been inactive successfully." :  "Item has been active successfully.";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
