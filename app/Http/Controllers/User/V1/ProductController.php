<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\PageModel, App\Infrastructure\AppConstant;
use App\Models\Product, App\Models\LuSize, App\Models\LuCategory, App\Models\LuCode, App\Models\SubSubCategory, App\Models\ProductPhoto, App\Models\LuSearchTerm;
use Validator, DB;

class ProductController extends BaseController
{
    /**
     * Display view
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Products";
        $categories = LuCategory::with(['subCategory' => function($q){
            $q->select('id','name','lu_category_id')->where('status',1);
        }])->where('status',1)->get()->toArray();

        $subSubCategories = SubSubCategory::where('status',1)->get()->toArray();
        $sizes = LuSize::get()->toArray();
        $codes = LuCode::get()->toArray();
        $searchTerms = LuSearchTerm::get()->toArray();
        $percentages = $this->getSizePercentage();

        $records = route('products.lists');
        $storeRecord = route('product.store');
        $destroyRecord = route('product.delete');
        $changeStatus = route('product.change_status');
        $storeWeightUpdate = route('product.store_weight');

        $getProductImagesRoute = route('product.images');
        $storeProductImageRoute = route('product.image.store');
        $deleteProductImageRoute = route('product.image.delete');


        $cataloguePath = '';
        $setting = \App\Models\Setting::find(1);
        if(!empty($setting) && !empty($setting->catalogue_name)){
            $cataloguePath = asset('public/'.INVOICE_FOLDER_NAME.'/'.$setting->catalogue_name);
        }

        $configData = array(
            'categories' => $categories,
            'subSubCategories' => $subSubCategories,
            'sizes' => $sizes,
            'codes' => $codes,
            'percentages' => $percentages,
            'cataloguePath' => $cataloguePath,
            'searchTerms' => $searchTerms,
            'singleCataloguePath' => asset('public/'.INVOICE_FOLDER_NAME.'/single-catalogue.pdf'),
        );

        return view('user/products',[
            'title'                     => $title, 
            'records'                   => $records,
            'storeRecord'               => $storeRecord,
            'destroyRecord'             => $destroyRecord,
            'storeWeightUpdate'         => $storeWeightUpdate,
            'configData'                => $configData,
            'changeStatus'              => $changeStatus,
            'getProductImagesRoute'     => $getProductImagesRoute, 
            'storeProductImageRoute'    => $storeProductImageRoute, 
            'deleteProductImageRoute'   => $deleteProductImageRoute,
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

        $query = Product::whereHas('category',function($q){
            $q->where('status',1);
        })->whereHas('size')->with(['category','size','subCategory' => function($q){
            $q->where('status',1);
        },'subSubCategory' => function($q){
            $q->where('status',1);
        }]);
        $searchParams = $reqData['Data']['SearchParams'];

        $query = $query->where(function($q) use($searchParams){
            if(array_key_exists('lu_category_id',$searchParams) && !empty($searchParams['lu_category_id'])){
                $categoryId = $searchParams['lu_category_id'];
                $q = $q->where('lu_category_id',$categoryId);
            }

            if(array_key_exists('lu_sub_category_id',$searchParams) && !empty($searchParams['lu_sub_category_id'])){
                $subCategoryId = $searchParams['lu_sub_category_id'];
                $q = $q->where('lu_sub_category_id',$subCategoryId);
            }

            if(array_key_exists('sub_sub_category_id',$searchParams) && !empty($searchParams['sub_sub_category_id'])){
                $subSubCategoryId = $searchParams['sub_sub_category_id'];
                $q = $q->where('sub_sub_category_id',$subSubCategoryId);
            }

            if(array_key_exists('lu_size_id',$searchParams) && !empty($searchParams['lu_size_id'])){
                $sizeId = $searchParams['lu_size_id'];
                $q = $q->where('lu_size_id',$sizeId);
            }

            if(array_key_exists('search_term_id',$searchParams) && !empty($searchParams['search_term_id'])){
                $searchTermId = $searchParams['search_term_id'];
                $q = $q->whereRaw(DB::RAW("JSON_CONTAINS(search_term_ids,{$searchTermId},'$')"));
            }

            if(array_key_exists('is_popular',$searchParams) && !empty($searchParams['is_popular'])){
                $isPopular = $searchParams['is_popular'] == 2 ? 0 : 1;
                $q = $q->where('is_popular',$isPopular);
            }

            if(array_key_exists('name',$searchParams) && !empty(trim($searchParams['name'])) && (trim($searchParams['name']) != "") ) {
                $name = $searchParams['name'];
                $q = $q->where('code','LIKE','%'.$name.'%')->orWhere('weight','LIKE','%'.$name.'%');
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
                $records[$key]['checked'] = false;
                $records[$key]['category_name'] = $item['category']['name'];
                $records[$key]['size_name'] = $item['size']['name'];
                $records[$key]['sub_category_name'] = !empty($item['sub_category']) ? $item['sub_category']['name'] : '';
                $records[$key]['sub_sub_category_name'] = !empty($item['sub_sub_category']) ? $item['sub_sub_category']['name'] : '';
                $records[$key]['weight'] = !empty($item['weight']) ? $item['weight'] : '0';
                $records[$key]['is_edit'] = false;
                $records[$key]['is_popular'] = $item['is_popular'] == 1 ? 'Yes' : 'No';
                $records[$key]['image'] = AppConstant::getImage($item['image']);
                $records[$key]['thumb_image'] = AppConstant::getImageThumb($item['thumb_image']);
                unset($records[$key]['category'],$records[$key]['size']);
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
        $checkFields = array('lu_category_id','code','sizes');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'lu_category_id' => 'required|numeric',
                'code' => 'required',
                'sizes' => 'required'
            ]);
            
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{

                $reqData['sizes'] = json_decode($reqData['sizes'],1);
                $sizes = array_filter($reqData['sizes'],function($a){
                    return $a['is_select'] == true;
                });

                $originalCode = $reqData['code'];
                $imagePath = AppConstant::storeImage($reqData['attachment']);

                foreach($sizes as $size){
                    //Check Code Exists
                    $codeExists = Product::where('code','LIKE',$originalCode.'%')->orderByDesc('id')->first();

                    if(!empty($codeExists)){
                        $lastNumber = str_replace($originalCode,"",$codeExists->code);
                        if(!empty($lastNumber)){
                            $reqData['code'] = $originalCode.($lastNumber + 1);
                        }else{
                            $reqData['code'] = $originalCode.'1';
                        }
                    }
                    $record = new Product;
                    $record->lu_category_id = $reqData['lu_category_id'];
                    $record->lu_sub_category_id = !empty($reqData['lu_sub_category_id']) ? $reqData['lu_sub_category_id'] : NULL;
                    $record->sub_sub_category_id  = !empty($reqData['sub_sub_category_id']) ? $reqData['sub_sub_category_id'] : NULL;
                    $record->code = $reqData['code'];
                    $record->weight = $size['weight'];
                    $record->search_term_ids = !empty($reqData['search_term_id']) ? json_encode(array_map('intval',$reqData['search_term_id'])) : NULL;

                    $thumbImagePath = $record->code.time().$imagePath;

                    \Image::make(AppConstant::getImage($imagePath))->resize($this->getWidthBasedOnPercentage($size['percentage']), null, function ($constraint) {
                            $constraint->aspectRatio();
                    })->save(storage_path('app/media/thumb/'.$thumbImagePath));

                    $record->image = $imagePath;
                    $record->percentage = $size['percentage'];
                    $record->image_width = $this->getWidthBasedOnPercentage($size['percentage']);
                    $record->thumb_image = $thumbImagePath;
                    $record->lu_size_id = $size['id'];
                    $record->save();
                }

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
                //AppConstant::deleteImage($record->image);
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
            if(!empty($productImages)){
                foreach($productImages as $productImageKey => $productImageValue){
                    $productImages[$productImageKey]['thumb_image'] = AppConstant::getImageThumb($productImageValue['image']);
                    $productImages[$productImageKey]['image'] = AppConstant::getImage($productImageValue['image']);
                }
            }
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
                $image = $record->image;
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
     * Store Weight
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWeight(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id','weight');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $record = Product::find($id);
            if(!empty($record)){
                $record->weight = $reqData['weight'];
                $record->save();

                $response->IsSuccess = true;
                $response->Message = "Weight has been updated.";
            }else{
                $response->Message = "Product not found.";
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Store Popular Product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePopularProduct(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('ids','status');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $ids = $reqData['ids'];
            $status = $reqData['status'];
            if(!is_array($ids)){
                $ids = explode(",", $ids);
            }
            
            if($status == 1){
                Product::whereIn('id',$ids)->update(['is_popular' => 1]);
                
                $deviceTokens = \App\Models\User::where('is_approved',2)->whereNotNull('device_token')->pluck('device_token')->toArray();

                if(!empty($ids) && !empty($deviceTokens)){
                    // Get all tokens

                    foreach($ids as $id){
                        $productDetail = Product::find($id);
                        if(!empty($productDetail)){
                            // Get all tokens
                            $message = "New popular product {$productDetail->code} has been added.";
                            $this->pushNotifications($deviceTokens,$message,$productDetail->id, AppConstant::getImage($productDetail->image));
                        }
                    }    
                }
            }else{
                Product::whereIn('id',$ids)->update(['is_popular' => 0]);
            }

            $response->IsSuccess = true;
            $response->Message =  trans('messages.updated_record');
        }
        else{
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
    public function changeStatus(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $record = Product::find($id);
            $record->status = !empty($reqData['checkedValue']) ? 1 : 0;
            $record->save();

            $response->IsSuccess = true;
            $response->Message = $record->status == 0 ?  "Product has been inactive successfully." :  "Product has been active successfully.";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}