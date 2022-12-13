<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Infrastructure\ServiceResponse;
use App\Http\Controllers\BaseController;
use App\Infrastructure\AppConstant;
use App\Models\Order;
use DB, PDF;

class InvoiceController extends BaseController
{
    /**
     * Generate Invoice
     * 
     * @param string encryptId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice($encryptID, Request $request)
    {
        $decryptEnquiryID = BaseController::getEncryptDecryptValue('decrypt', $encryptID);
        $orderId =  BaseController::getExplodeValue($decryptEnquiryID, AppConstant::$orderId);
        
        if($orderId > 0){
            $isShort = str_contains($request->path(), 'generate-short-invoice');

            //Get Order Details
            $orderDetail = Order::whereHas('details')->with('details.product','user')->find($orderId);

            if(!empty($orderDetail)){
                $orderInfo = $orderDetail->toArray();
                $userName = $orderDetail->user->name;
                $userName = !empty($isShort) ? substr($userName,0,3) : $userName;
                $invoiceNo = !empty($isShort) ? 
                'IN_SHORT_'.substr(str_replace(" ","",strtoupper(trim($orderDetail->user->name))),0,5).'_'.date('Y').''.($orderDetail->id) :
                'IN_'.substr(str_replace(" ","",strtoupper(trim($orderDetail->user->name))),0,5).'_'.date('Y').''.($orderDetail->id);
                $fileName = $invoiceNo.'.pdf';

                $totalWeight = array_sum(array_map(function($a){
                    return $a['product']['weight'] * $a['quantity'];
                }, $orderInfo['details']));

                foreach($orderInfo['details'] as $key => $item){
                    $orderInfo['details'][$key]['product']['thumb_image'] = AppConstant::getImageThumb($item['product']['thumb_image']);
                }

                $pdf = PDF::loadView('pdf/invoice',['orderInfo' => $orderInfo, 'totalWeight' => $totalWeight,'userName' => $userName,'isShort' => $isShort]);
                $pdf->save(INQUIRY_FOLDER_PATH.$fileName);

                !empty($isShort) ? $orderDetail->invoice_short_name = $fileName : $orderDetail->invoice_name = $fileName;
                $orderDetail->save();
            }
        }
        return redirect()->route('orders.index');
    }

    /**
     * Generate Invoice
     * 
     * @param string encryptId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function generateCatalogue(Request $request)
    {
        $searchParams = $request->all();
        $query = \App\Models\Product::whereHas('size')->with('size');

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

            if(array_key_exists('is_popular',$searchParams) && !empty($searchParams['is_popular'])){
                $isPopular = $searchParams['is_popular'] == 2 ? 0 : 1;
                $q = $q->where('is_popular',$isPopular);
            }

            if(array_key_exists('name',$searchParams) && !empty(trim($searchParams['name'])) && (trim($searchParams['name']) != "") ) {
                $name = $searchParams['name'];
                $q = $q->where('code','LIKE','%'.$name.'%')->orWhere('weight','LIKE','%'.$name.'%');
            }
        });
        
        $records = $query->get()->toArray();

        if (count($records)>0) {
            foreach($records as $key => $item) {
                $records[$key]['size_name'] = $item['size']['name'];
                $records[$key]['thumb_image'] = AppConstant::getImageThumb($item['thumb_image']);
                unset($records[$key]['size']);
            }
        }

        $array1 = $array2 = [];
        
        if(!empty($records)){
            $checkLength = count($records);
            if($checkLength > 1){
                list($array1, $array2) = array_chunk($records, ceil(count($records) / 2));
            }else{
                $array1 = $records;
            }
        }

        $pdf = PDF::loadView('pdf/catalogue',['array1' => $array1,'array2' => $array2]);
        //return $pdf->stream();
        $fileName = 'catalogue.pdf';
        $pdf->save(INQUIRY_FOLDER_PATH.$fileName);

        $setting = \App\Models\Setting::find(1);
        $setting->catalogue_name = $fileName;
        $setting->save();
        
        return redirect()->route('products.index');
    }

    /**
     * Generate Main Invoice
     * 
     * @param string encryptId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function generateSingleCatalogue(Request $request)
    {
        $searchParams = $request->all();
        $query = \App\Models\Product::whereHas('size')->with('size');

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

            if(array_key_exists('is_popular',$searchParams) && !empty($searchParams['is_popular'])){
                $isPopular = $searchParams['is_popular'] == 2 ? 0 : 1;
                $q = $q->where('is_popular',$isPopular);
            }

            if(array_key_exists('name',$searchParams) && !empty(trim($searchParams['name'])) && (trim($searchParams['name']) != "") ) {
                $name = $searchParams['name'];
                $q = $q->where('code','LIKE','%'.$name.'%')->orWhere('weight','LIKE','%'.$name.'%');
            }
        });
        
        $records = $query->get()->toArray();

        if (count($records)>0) {
            foreach($records as $key => $item) {
                $records[$key]['size_name'] = $item['size']['name'];
                $records[$key]['thumb_image'] = AppConstant::getImageThumb($item['thumb_image']);
                unset($records[$key]['size']);
            }
        }

        $pdf = PDF::loadView('pdf/single-catalogue',['records' => $records]);
        //return $pdf->stream();
        $fileName = 'single-catalogue.pdf';
        $pdf->save(INQUIRY_FOLDER_PATH.$fileName);
        return redirect()->route('products.index');
    }
}