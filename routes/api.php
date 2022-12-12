<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => API_VERSION, 'namespace' => API_NAMESPACE_PREFIX],function(){
    Route::controller(Auth\LoginController::class)->group(function () {
        Route::post('kk-login','postLogin');
    });

    Route::group(array('middleware' => 'auth.api'),function(){
        
        //Profile
        Route::controller(DashboardController::class)->group(function () {
            Route::get('check-approval','getCheckApproval');
            Route::get('logout','getLogout');
            Route::get('categories','getCategory');
            Route::post('sub-categories','getSubCategory');
            Route::get('dashboard-data','getDashboardData');
        });

        //Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::post('store-profile','storeProfile');
        });

        //Products
        Route::controller(ProductController::class)->group(function () {
            Route::post('get-products','getProducts');
            
            //Product Detail
            Route::post('product-detail','getProductDetail');
            
            //Add To Cart
            Route::post('add-to-cart','addToCart');
            Route::get('cart-details','cartDetails');
            Route::post('update-quantity','updateQuantity');

            //Checkout
            Route::get('checkout','storeCheckout');
        });

        //Orders
        Route::controller(OrderController::class)->group(function () {
            Route::post('orders','getMyOrders');

            //Order details
            Route::post('order-details','getOrderDetail');
            Route::post('cancel-order','storeCancelledOrder');
            
            //Add To Cart
            Route::post('update-order','updateOrder');
        });
    });
});
