<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\CommonController;
use App\Infrastructure\ServiceResponse;
use App\Infrastructure\PageModel;
use Validator, DB;

class SubTypesController extends BaseController
{

    /**
     * Configuration Model
     */
    protected $type = [
        'sub-category' => array(
            'modal' => 'App\Models\LuSubCategory',
            'parent_id' => 'lu_category_id',
            'relation' => 'category',
        )
    ];

    /**
     * Configuration Model Data
     */
    protected function parentData($subType){
        $subTypeArr = ['category' => array(
                'txt' => "Category",
                'parentData' => \App\Models\LuCategory::select('id','name')->get()->toArray(),
                'placeholder_txt' => "Select Category",
                'child_txt' => "Select Sub Category",
                'url' => route('lookup.types.index',['type'=> $subType]),
            )
        ];

        if(empty($subType) || !array_key_exists($subType, $subTypeArr) ){
            $subType = 'category';
        }
        return $subTypeArr[$subType];
    }

   	/**
     * Display view
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type,$subType)
    {
        if (!isset($this->type[$subType])) {
            return redirect()->route('dashboard');
        }

        $typeData = $this->type[$subType];
        $parentName = $typeData['relation'];

        $typeParentData = $this->parentData($type);
        $parentData = $typeParentData['parentData'];
        $parentPlaceholder = $typeParentData['placeholder_txt'];
        $title = $typeParentData['child_txt'];
        $type = $typeParentData['txt'];
        $typeURL = $typeParentData['url'];
        
        $records = route('lookup.sub_types.list',['type' => $type,'subType' => $subType]);
        $storeRecord = route('lookup.sub_type.store',['type' => $type,'subType' => $subType]);
        $destoryRecord = route('lookup.sub_type.delete',['type' => $type,'subType' => $subType]);
        $changeStatus = route('lookup.sub_type.change_status',['type' => $type,'subType' => $subType]);

        return view('user/sub-types',['title' => $title,'records' => $records, 'storeRecord' => $storeRecord, 'destoryRecord' => $destoryRecord, 'changeStatus' => $changeStatus, 'parentData' => $parentData, 'parentName' => $parentName, 'parentPlaceholder' => $parentPlaceholder, 'type' => $type, 'typeURL' => $typeURL]);
    }

    /**
     * Display the list resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxList(Request $request,$type,$subType)
    {
        $response = new ServiceResponse();
        $reqData = $request->all();
        $pageIndex = $reqData['Data']['PageIndex'];
        $pageSize = $reqData['Data']['PageSize'];
        $limit = $pageSize;
        $offset = ($pageIndex - 1) * $pageSize;
        
        $typeData = $this->type[$subType];
        $getRelation = $typeData['relation'];

        $query = $typeData['modal']::whereHas($getRelation)->with([$getRelation => function($q){
            $q->select('id','name');
        }]);

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
        $records = $query->orderBy('id','asc')->take($limit)->offset($offset)->get()->toArray();

        if (count($records)>0) {
            $index = ($pageSize * ($pageIndex-1)) + 1;
            foreach ($records as $key => $item) {
                $typeData = $this->type[$subType];
                $records[$key]['Index'] = $index++;
                $records[$key]['type'] = $type;
                $records[$key]['parent_value'] = $item[$typeData['relation']]['name'];
                $records[$key]['parent_id'] = $item[$typeData['parent_id']];
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
    public function store(Request $request,$type,$subType){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('parent_id','name');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $parentValue = $reqData['parent_id'];

            $typeData = $this->type[$subType];
            $modal = $typeData['modal'];
            $tableName = $modal::getTableName();
            $parentFieldName = $typeData['parent_id'];
            
            $validator = Validator::make($reqData, [
                'name' => 'required|unique:'.$tableName.',name,'.$id.',id,'.$parentFieldName.','.$parentValue
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $updated_at = $this->getDateTime();
                
                DB::beginTransaction();
                $record = $id > 0 ? $modal::find($id) : new $modal;
                $record->{$parentFieldName} = $parentValue;
                $record->name = $reqData['name'];
                $record->save();
                DB::commit();

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
    public function destroy(Request $request,$type,$subType){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $typeData = $this->type[$subType];
            $modal = $typeData['modal'];
            $tableName = $modal::getTableName();

            $record = $modal::find($id);

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
    public function changeStatus(Request $request,$type,$subType){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $typeData = $this->type[$subType];
            $modal = $typeData['modal'];
            $record = $modal::find($id);
            $record->status = !empty($reqData['checkedValue']) ? $reqData['checkedValue'] : 0;
            $record->save();

            $response->IsSuccess = true;
            $response->Message = $record->status == 1 ?  trans('messages.enable_record') :  trans('messages.disabled_record');
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
