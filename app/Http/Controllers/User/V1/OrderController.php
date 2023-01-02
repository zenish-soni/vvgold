<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\PageModel, App\Infrastructure\AppConstant;
use App\Models\User, App\Models\Order;
use Validator, DB;

class OrderController extends BaseController
{
    /**
     * Display view
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Orders";
        $records = route('orders.lists');
        $changeStatus = route('order.change_status');
        $users = User::where('user_type_id',2)->get();
        if(!empty($users)){
            foreach($users as $key => $value){
                $users[$key]['name'] = $value['name'].' - '.$value['phone_number'];
            }
        }

        return view('user/orders',[
            'title'         => $title, 
            'records'       => $records,
            'users'         => $users,
            'changeStatus'  => $changeStatus
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

        $query = Order::whereHas('details')->whereHas('user')->with('details','user');
        $searchParams = $reqData['Data']['SearchParams'];

        $query = $query->where(function($q) use($searchParams){
            if(array_key_exists('user_id',$searchParams) && !empty($searchParams['user_id'])){
                $userId = $searchParams['user_id'];
                $q = $q->where('user_id',$userId);
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

                $totalWeight = array_sum(array_map(function($a){
                    return $a['weight'] * $a['quantity'];
                }, $item['details']));

                foreach($item['details'] as $key2 => $item2){
                    $records[$key]['details'][$key2]['image'] = AppConstant::getImage($item2['image']);
                }
                $records[$key]['total_weight'] = $totalWeight;
                $records[$key]['created_at'] = config_date($item['created_at']);

                $encryptId = $this->getEncryptDecryptID('encrypt',AppConstant::$orderId.'='.$item['id']);
                $records[$key]['invoiceUrl'] = route('invoices.generate_invoice',$encryptId);
                $viewInvoice = '';
                if(!empty($item['invoice_name'])){
                    $viewInvoice = asset('public/'.INVOICE_FOLDER_NAME.'/'.$item['invoice_name']);
                }
                $records[$key]['viewInvoice'] = $viewInvoice;

                //Short Invoice
                $records[$key]['invoiceShortUrl'] = route('invoices.generate_short_invoice',$encryptId);
                $viewShortInvoice = '';
                if(!empty($item['invoice_short_name'])){
                    $viewShortInvoice = asset('public/'.INVOICE_FOLDER_NAME.'/'.$item['invoice_short_name']);
                }
                $records[$key]['viewShortInvoice'] = $viewShortInvoice;
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
     * Change user status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request){
        $reqData = $request->all();
        $response = new ServiceResponse;
        $checkFields = array('id','status');
        $checkRequiredField = $this->checkRequestData($checkFields,$reqData);

        if($checkRequiredField == 'SUCC100'){
            $id = $reqData['id'];

            Order::where('id',$id)->update(['status' => $reqData['status']]);
            $response->IsSuccess = true;
            $response->Message = "Status has been changed successfully.";
        }else{
            $response->Message = $checkRequiredField;
        }
        return $this->GetJsonResponse($response);
    }
}