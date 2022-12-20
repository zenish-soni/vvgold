<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse;
use App\Infrastructure\PageModel;
use Validator, DB;

class TypesController extends BaseController
{

    /**
     * Configuration Model
     */
    protected $typeModel = [
        'size' => 'App\Models\LuSize',
        'code' => 'App\Models\LuCode',
        'search-term' => 'App\Models\LuSearchTerm',
    ];

    /**
     * Configuration Title
     */
    protected function getTitle($type){
        $typeTitles = [
            'size' => "Size",
            'code' => "Code",
            'search-term' => "Search Term",
        ];  
        if(empty($type) || !array_key_exists($type, $typeTitles) ){
            $type = 'size';
        }
        return $typeTitles[$type];
    } 

   	/**
     * Display view
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        if (!isset($this->typeModel[$type])) {
            return redirect()->route('dashboard');
        }
        $title = $this->getTitle($type);
        $records = route('lookup.types.list',['type' => $type]);
        $storeRecord = route('lookup.type.store',['type' => $type]);
        $destoryRecord = route('lookup.type.delete',['type' => $type]);
        $changeStatus = route('lookup.type.change_status',['type' => $type]);

        return view('user/types',['title' => $title,'records' => $records, 'storeRecord' => $storeRecord, 'destoryRecord' => $destoryRecord, 'changeStatus' => $changeStatus]);
    }

    /**
     * Display the list resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxList(Request $request,$type)
    {
        $response = new ServiceResponse();
        $reqData = $request->all();
        $pageIndex = $reqData['Data']['PageIndex'];
        $pageSize = $reqData['Data']['PageSize'];
        $limit = $pageSize;
        $offset = ($pageIndex - 1) * $pageSize;

        $query = $this->typeModel[$type]::query();
        if(array_key_exists('name',$reqData['Data']['SearchParams']) && !empty(trim($reqData['Data']['SearchParams']['name'])) && (trim($reqData['Data']['SearchParams']['name']) != "") ) {
            $name =  trim($reqData['Data']['SearchParams']['name']);
            $query = $query->where('name','LIKE','%'.$name.'%');
        }

        $countQuery = $query;
        $countQueryCount = $countQuery->count();
        $records = $query->orderByDesc('id')->take($limit)->offset($offset)->get()->toArray();

        if (count($records)>0) {
            $index = ($pageSize * ($pageIndex-1)) + 1;
            foreach ($records as $key => $item) {
                $records[$key]['Index'] = $index++;
                $records[$key]['type'] = $type;
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
    public function store(Request $request,$type){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('name');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $tableName = $this->typeModel[$type]::getTableName();
            
            $validator = Validator::make($reqData, [
                'name' => 'required|unique:'.$tableName.',name,'.$id.',id'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                
                $record = $id > 0 ? $this->typeModel[$type]::find($id) : new $this->typeModel[$type];
                $record->name = $reqData['name'];
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
    public function destroy(Request $request,$type){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $tableName = $this->typeModel[$type]::getTableName();
            $record = $this->typeModel[$type]::find($id);

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
    public function changeStatus(Request $request,$type){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $record = $this->typeModel[$type]::find($id);
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
