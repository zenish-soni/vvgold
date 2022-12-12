<?php

namespace App\Http\Controllers\User\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Infrastructure\ServiceResponse, App\Infrastructure\AppConstant;
use DB, Auth, Hash, Session;

class DashboardController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDashboard(){
    	$title = 'Dashboard';
        $authDetail = Auth::user();
        $authID = $authDetail->id;

        $totalOrder = \App\Models\Order::count();
        $totalUser = \App\Models\User::where('user_type_id',2)->count();
        $totalProduct = \App\Models\Product::count();
        
    	return view('user/dashboard',[
            'title'         => $title,
            'totalOrder'    => $totalOrder,
            'totalUser'     => $totalUser,
            'totalProduct'  => $totalProduct,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function convertProductImages(){
        /*$products = \App\Models\Product::where('id','=',85)->get();
        if(!empty($products)){
            foreach($products as $product){
                $imagePath = $product->getRawOriginal('image');
                \Image::make(AppConstant::getImage($imagePath))->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(storage_path('app/media/thumb/'.$imagePath));
            }
        }*/

        $products = \App\Models\ProductPhoto::get();
        if(!empty($products)){
            foreach($products as $product){
                $imagePath = $product->getRawOriginal('image');
                \Image::make(AppConstant::getImage($imagePath))->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(storage_path('app/media/thumb/'.$imagePath));
            }
        }
    }
    
    /**
     * Manage Logout
     *
     */
    public function getLogout(){
        Auth::logout();
        Session::flash('msg', "You are successfully logged out.");
        return redirect()->route('login');
    }
}
