<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\Product, App\Models\UserCart, App\Models\Order, App\Models\OrderDetail;
use Auth, Validator, DB;

class ProductController extends BaseController
{
    /**
     * Get Product Filters
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('limit');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $categoryId = $reqData['category_id'];
            $limit = $reqData['limit'];
            $offset = (!empty($reqData) && !empty($reqData['offset'])) ? $reqData['offset'] : 0;

            $validator = Validator::make($reqData, [
                'limit' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                
                $query = Product::active()->whereHas('category',function($q){
                    $q->where('status',1);
                })->whereHas('size')->with(['category','size','subCategory' => function($q){
                    $q->where('status',1);
                },'subSubCategory' => function($q){
                    $q->where('status',1);
                }]);

                if(!empty($reqData['lu_category_id'])){
                    $query = $query->where('lu_category_id',$reqData['lu_category_id']);
                }

                if(!empty($reqData['search_term_id'])){
                    $searchTermId = $reqData['search_term_id'];
                    $query = $query->whereRaw(DB::RAW("JSON_CONTAINS(search_term_ids,{$searchTermId},'$')"));
                }

                if(!empty($reqData['sub_category_id'])){
                    $query = $query->where('lu_sub_category_id',$reqData['sub_category_id']);
                }

                if(!empty($reqData['sub_sub_category_id'])){
                    $query = $query->where('sub_sub_category_id',$reqData['sub_sub_category_id']);
                }

                $records = $query->orderByDesc('id')->take($limit)->offset($offset)->get()->toArray();

                if(!empty($records)){
                    foreach($records as $key => $record){
                        $records[$key]['description'] = $this->convertNullToChar($record['description']);
                        $records[$key]['lu_sub_category_id'] = $this->convertNullToChar($record['lu_sub_category_id'],1);
                        $records[$key]['sub_sub_category_id'] = $this->convertNullToChar($record['sub_sub_category_id'],1);
                        $records[$key]['weight'] = !empty($record['weight']) ? (float)($record['weight']) : 0;
                        $records[$key]['image'] = AppConstant::getImage($record['image']);
                        $records[$key]['size_name'] = !empty($record['size']) ? $record['size']['name'] : '';
                        unset($records[$key]['size'],$records[$key]['category'],$records[$key]['sub_sub_category'],$records[$key]['sub_category']);
                    }
                }

                $response->Data = $records;
                $response->offset = (count($records) >= $limit ) ? ($offset + $limit) : 0;
                $response->IsSuccess = true;
            }
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Add To Cart
     * Method POST
     *
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('id','quantity');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'id' => 'required',
                'quantity' => 'required|numeric',
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{

                $authId = Auth::guard('api')->user()->id;
                $quantity = $reqData['quantity'];
                $productId = $reqData['id'];

                //Check Product Exist or not
                $cartDetail = UserCart::where(['user_id' => $authId, 'product_id' => $productId])->first();

                if(empty($cartDetail)){
                    $cartDetail = new UserCart;
                    $cartDetail->user_id = $authId;
                    $cartDetail->product_id = $productId;    
                }
                $cartDetail->quantity = $quantity;
                $cartDetail->save();

                $response->total_item = UserCart::where('user_id',$authId)->count();

                $response->IsSuccess = true;
                $response->Message = "Product has been added successfully.";
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Update Quantity
     * Method POST
     *
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'id' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $id = $reqData['id'];
                $authId = Auth::guard('api')->user()->id;

                $cartDetail = UserCart::find($id);

                if(!empty($cartDetail)){
                    $quantity = $reqData['quantity'];

                    if(empty($quantity)){
                        UserCart::where('id',$id)->delete();
                    }else{
                        $cartDetail->quantity = $quantity;    
                        $cartDetail->save();
                    }

                    $response->total_item = UserCart::where('user_id',$authId)->count();
                    $response->IsSuccess = true;
                }else{
                    $response->Message = "No item found";
                }
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Cart Details
     * Method GET
     *
     * @return \Illuminate\Http\Response
     */
    public function cartDetails(){
        $response = new ServiceResponse;
        $authId = Auth::guard('api')->user()->id;

        //Get User Cart Details
        $records = UserCart::whereHas('product')->with('product.size')->where('user_id',$authId)->get()->toArray();
        
        $totalWeight = 0;

        if(!empty($records)){
            $totalWeight = array_sum(array_map(function($a){
                return $a['product']['weight'] * $a['quantity'];
            }, $records));

            foreach($records as $key => $record){
                $image = AppConstant::getImage($record['product']['image']);
                $records[$key]['image'] = $image;
                $records[$key]['code'] = $record['product']['code'];
                $records[$key]['weight'] = $record['product']['weight'];
                $records[$key]['size'] = $record['product']['size']['name'];
                unset($records[$key]['created_at'],$records[$key]['updated_at'],$records[$key]['product']);
            }
        }

        $response->Data = $records;
        $response->total_weight = $totalWeight;
        $response->total_item = UserCart::where('user_id',$authId)->count();

        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }

    /**
     * Store Checkout
     * Method POST
     *
     * @return \Illuminate\Http\Response
     */
    public function storeCheckout(Request $request){
        $response = new ServiceResponse;
        $authId = Auth::guard('api')->user()->id;

        $records = UserCart::where('user_id',$authId)->get();
        if(!empty($records)){
            $order = new Order;
            $order->user_id = $authId;
            $order->status = 1;
            $order->save();

            $orderId = $order->id;

            foreach($records as $item){
                $productId = $item['product_id'];
                $productDetail = Product::find($productId);

                if(!empty($productDetail) && !empty($productDetail->status)){
                    $orderDetail = new OrderDetail;
                    $orderDetail->order_id = $orderId;
                    $orderDetail->product_id = $productId;
                    $orderDetail->quantity = $item['quantity'];
                    $orderDetail->category_name = !empty($productDetail->category) ? $productDetail->category->name : '';
                    $orderDetail->sub_category_name = !empty($productDetail->subCategory) ? $productDetail->subCategory->name : '';
                    $orderDetail->sub_sub_category_name = !empty($productDetail->subSubCategory) ? $productDetail->subSubCategory->name : '';
                    $orderDetail->size_name = $productDetail->size->name;
                    $orderDetail->code = $productDetail->code;
                    $orderDetail->image = $productDetail->image;
                    $orderDetail->thumb_image = $productDetail->thumb_image;
                    $orderDetail->description = $productDetail->description;
                    $orderDetail->weight = $productDetail->weight;
                    $orderDetail->search_term_ids = $productDetail->search_term_ids;
                    $orderDetail->percentage = $productDetail->percentage;
                    $orderDetail->image_width = $productDetail->image_width;
                    $orderDetail->save();    
                }
            }

            UserCart::where('user_id',$authId)->delete();
            $response->IsSuccess = true;
        }else{
            $response->Message = "Your cart is empty";
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Product Detail
     * Method POST
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductDetail(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'id' => 'required'
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $id = $reqData['id'];
                $productDetail = Product::find($id);
                if(!empty($productDetail) && $productDetail->status == 1){
                    $response->id = $productDetail->id;
                    $response->percentage = $productDetail->percentage;
                    $response->image_width = $productDetail->image_width;
                    $response->category_name = $productDetail->category->name;
                    $response->sub_category_name = !empty($productDetail->subCategory) ? $productDetail->subCategory->name : '';
                    $response->sub_sub_category_name = !empty($productDetail->subSubCategory) ? $productDetail->subSubCategory->name : '';
                    $response->size_name = !empty($productDetail->size) ? $productDetail->size->name : '';
                    $response->code = $productDetail->code;
                    $image = AppConstant::getImage($productDetail->image);
                    $response->image = $image;
                    $response->description = $this->convertNullToChar($productDetail->description);
                    $response->weight = $productDetail->weight;

                    $images = array(array('image' => $image));
                    if(!empty($productDetail->photos)){
                        foreach($productDetail->photos as $photo){
                            array_push($images,array('image' => AppConstant::getImage($photo['image'])));
                        }
                    }
                    $response->images = $images;
                    $response->IsSuccess = true;
                }else{
                    $response->Message = "Product not found";
                }                
            }
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}
