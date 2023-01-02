<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use App\Models\Order, App\Models\OrderDetail;
use Auth, Validator;

class OrderController extends BaseController
{
    /**
     * Get Product Filters
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMyOrders(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('limit');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $authId = Auth::guard('api')->user()->id;

            $limit = $reqData['limit'];
            $offset = (!empty($reqData) && !empty($reqData['offset'])) ? $reqData['offset'] : 0;
                
            $records = Order::whereHas('details')->with('details')->withCount('details')->where('user_id',$authId)->orderByDesc('id')->take($limit)->offset($offset)->get()->toArray();

            if(!empty($records)){
                foreach($records as $key => $record){
                    $totalWeight = array_sum(array_map(function($a){
                        return $a['weight'] * $a['quantity'];
                    }, $record['details']));
                    
                    $records[$key]['invoice_number'] = $record['id'];
                    $records[$key]['total_weight'] = $totalWeight;
                    $records[$key]['created_date'] = config_date($record['created_at']);
                    $records[$key]['created_time'] = config_time($record['created_at']);
                    $records[$key]['status_name'] = $this->getStatusName($record['status']);

                    /*foreach($record['details'] as $key2 => $detail){
                        $records[$key]['details'][$key2]['code'] = $detail['product']['code'];
                        $records[$key]['details'][$key2]['weight'] = $detail['product']['weight'];
                        $records[$key]['details'][$key2]['image'] = AppConstant::getImage($detail['product']['image']);
                        $records[$key]['details'][$key2]['size_name'] = !empty($detail['product']['size']) ? $detail['product']['size']['name'] : '';
                        unset($records[$key]['details'][$key2]['product'], $records[$key]['details'][$key2]['created_at'], $records[$key]['details'][$key2]['updated_at']);
                    }*/
                    unset($records[$key]['details'],$records[$key]['created_at'],$records[$key]['user_id'],$records[$key]['updated_at'],$records[$key]['invoice_name'],$records[$key]['invoice_short_name']);
                }
            }

            $response->Data = $records;
            $response->offset = (count($records) >= $limit ) ? ($offset + $limit) : 0;
            $response->IsSuccess = true;
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Order Detail
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOrderDetail(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            $totalWeight = 0;
                
            $orderDetail = OrderDetail::where('order_id',$id)->get()->toArray();

            if(!empty($orderDetail)){
                $totalWeight = array_sum(array_map(function($a){
                    return $a['weight'] * $a['quantity'];
                }, $orderDetail));

                foreach($orderDetail as $key => $detail){
                    $orderDetail[$key]['code'] = $detail['code'];
                    $orderDetail[$key]['weight'] = $detail['weight'];
                    $orderDetail[$key]['image'] = AppConstant::getImage($detail['image']);
                    unset($orderDetail[$key]['created_at'], $orderDetail[$key]['updated_at']);
                }

                $response->total_weight = $totalWeight;
            }

            $response->Data = $orderDetail;
            $response->IsSuccess = true;
        }
        else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }

    /**
     * Get Order Detail
     * Method POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCancelledOrder(Request $request){
        $response = new ServiceResponse();
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);
        
        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];
                
            Order::where('id',$id)->update(['status' => 3]);

            $response->IsSuccess = true;
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
    public function updateOrder(Request $request){
        $response = new ServiceResponse;
        $reqData = $request->all();
        $checkFields = array('id');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $validator = Validator::make($reqData, [
                'id' => 'required',
            ]);
           
            if ($validator->fails()) {
                $response->Message = $this->getValidationMessagesFormat($validator->messages());
            }else{
                $orderDetailId = $reqData['id'];

                //Check Product Exist or not
                $orderDetail = OrderDetail::find($orderDetailId);

                if(!empty($orderDetail)){

                    //Check Order Details
                    $orderInfo = Order::find($orderDetail->order_id);
                    if(!empty($orderInfo)){
                        $quantity = !empty($reqData['quantity']) ? $reqData['quantity'] : 0;

                        if(empty($quantity)){
                            OrderDetail::where('id',$orderDetailId)->delete();
                        }else{
                            $orderDetail->quantity = $quantity;    
                            $orderDetail->save();
                        }

                        $response->IsSuccess = true;
                        $response->Message = "Order has been updated successfully.";    
                    }else{
                        $response->Message = "Order Info not found";
                    }
                }else{
                    $response->Message = "Order detail not found";
                }
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
        
        if(!empty($records)){
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
        $response->total_item = UserCart::where('user_id',$authId)->count();

        $response->IsSuccess = true;
        return $this->GetJsonResponse($response);
    }
}
