<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\PageModel, App\Infrastructure\AppConstant;
use App\Models\User;
use Validator, DB;

class UserController extends BaseController
{
    /**
     * Display view
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "User";
        $records = route('users.lists');
        $changeStatus = route('users.change_status');
        $changeApproved = route('users.change_approved');

        return view('user/users',[
            'title'         => $title, 
            'records'       => $records,
            'changeStatus'  => $changeStatus,
            'changeApproved'=> $changeApproved,
        ]);
    }

    /**
     * Display the list resource.
     *
     * @param  string  $type
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

        $query = User::where('id','!=',1);
        $searchParams = $reqData['Data']['SearchParams'];

        $query = $query->where(function($q) use($searchParams){
            if(array_key_exists('name',$searchParams) && !empty(trim($searchParams['name'])) && (trim($searchParams['name']) != "")){
                $name = $searchParams['name'];
                $q = $q->where('name','LIKE','%'.$name.'%')->orWhere('email','LIKE','%'.$name.'%')->orWhere('phone_number','LIKE','%'.$name.'%')->orWhere('company_name','LIKE','%'.$name.'%');
            }
        });

        $countQuery = $query;
        $countQueryCount = $countQuery->count();

        if(!empty($reqData['Data']['SortDirection']) && !empty($reqData['Data']['SortIndex'])){
            $query = $query->orderBy($reqData['Data']['SortIndex'],$reqData['Data']['SortDirection']);
        }else{
            $query = $query->orderByDesc('id');
        }
        $records = $query->take($limit)->offset($offset)->get()->toArray();
        
        if (count($records)>0) {
            $index = ($pageSize * ($pageIndex-1)) + 1;
            foreach($records as $key => $item) {
                $records[$key]['Index'] = $index++;
                $records[$key]['business_photo'] = !empty($item['business_photo']) ? AppConstant::getImage($item['business_photo']) : NULL;
                $records[$key]['second_business_photo'] = !empty($item['second_business_photo']) ? AppConstant::getImage($item['second_business_photo']) : NULL;
                $records[$key]['approved_status'] = $item['is_approved'] == 2 ? 1 : 0;
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
     * Display the list resource.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('product_category_id','category_id','company_name','material_id','name','size_id','stock_type_id','surface_id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $validator = Validator::make($reqData, [
                'product_category_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'material_id' => 'required|numeric',
                'size_id' => 'required|numeric',
                'stock' => 'required|numeric',
                'stock_type_id' => 'required|numeric',
                'surface_id' => 'required|numeric',
                'company_name' => 'required',
                'name' => 'required'
            ]);
            
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $record = !empty($id) ? Product::find($id) : new Product;
                $record->product_category_id = $reqData['product_category_id'];
                $record->category_id = $reqData['category_id'];
                $record->size_id = $reqData['size_id'];
                $record->material_id = $reqData['material_id'];
                $record->surface_id = $reqData['surface_id'];
                $record->stock_type_id = $reqData['stock_type_id'];
                $record->name = $reqData['name'];
                $record->company_name = $reqData['company_name'];
                $record->stock = $reqData['stock'];
                $record->availability_type = $reqData['availability_type'];
                $record->stock_details = !empty($reqData['stock_details']) ? $reqData['stock_details'] : NULL;
                if(!empty($reqData['attachment'])){
                    if(!empty($id)){
                        AppConstant::deleteImage($record->image);
                        AppConstant::deleteThumbImage($record->image);
                    }
                    $imagePath = AppConstant::storeImage($reqData['attachment']);
                    $record->image = $imagePath;
                    //Open and resize an image file
                    \Image::make(AppConstant::getImage($imagePath))->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path('app/media/thumb/'.$imagePath));
                }
                $record->save();

                $response->IsSuccess = true;
                $response->Message =  !empty($id) ? "Record has been udpated successfully." : "Record has been added successfully.";
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
    public function delete(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
            $record = Product::find($id);

            if(!empty($record)){
                AppConstant::deleteImage($record->image);
                $record->delete();

                $response->IsSuccess = true;
                $response->Message = "Record has been deleted successfully.";
            }else{
                $response->Message = "Record not found";
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Product Images
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductImages(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $productID = $reqData['id'];
            /*$photos = array();*/
            $productImages = ProductPhoto::where('product_id',$productID)->select('id','image')->get()->toArray();
            /*if(!empty($productImages)){
                foreach($productImages as $productImageKey => $productImageValue){
                    $productImage = $productImageValue->getRawOriginal('image');
                    array_push($photos,array('id' => $productImageValue['id'], 'image' => AppConstant::getImage($productImage)));
                }
            }*/
            $response->Data = $productImages;
            $response->IsSuccess = true;
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Store Product Image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProductImage(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('product_id','image');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $productID = $reqData['product_id'];
            $images = $reqData['image'];
            //Multiple File Upload
            if(!empty($images)){
                foreach($images as $image){
                    $record = new ProductPhoto;
                    $record->product_id = $productID;
                    $imagePath = AppConstant::storeImage($image);

                    \Image::make(AppConstant::getImage($imagePath))->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path('app/media/thumb/'.$imagePath));

                    $record->image = $imagePath;
                    $record->created_at = $this->getDateTime();
                    $record->save();      
                }
            }

            //Single File Update
            /*$record = new ProductPhoto;
            $record->product_id = $productID;
            if($request->file('image')){
                $record->image = AppConstant::storePublicImage($request->file('image'));
            }
            $record->created_at = $this->getDateTime();
            $record->save();*/
            
            $response->IsSuccess = true;
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Delete Product Image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteProductImage(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $record = ProductPhoto::find($id);
            if(!empty($record)){
                $image = $record->getRawOriginal('image');
                AppConstant::deleteImage($image);
                AppConstant::deleteThumbImage($image);
                $record->delete();
                $response->IsSuccess = true;
            }else{
                $response->Message = "Product image not found";
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

            $record = User::find($id);
            $record->user_type_id = !empty($reqData['checkedValue']) ? 1 : 2;
            $record->save();

            $response->IsSuccess = true;
            $response->Message = $record->user_type_id == 1 ?  "Assign admin roles" :  "remove admin roles";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Change approved status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeApproved(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $record = User::find($id);
            $record->is_approved = !empty($reqData['checkedValue']) ? 2 : 3;
            $record->save();

            $response->IsSuccess = true;
            $response->Message = $record->is_approved == 2 ?  "User has been approved successfully." :  "User has been rejected successfully.";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}