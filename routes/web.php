<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('privacy-policy',function(){
    return view('privacy-policy');
});

Route::get('our-privacy-policy',function(){
    return view('privacy-policy');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('login','index')->name('login');
    Route::post('check-login','postManageLogin')->name('manage_login');
});

Route::group(['prefix' => USER_NAME, 'namespace' => USER_NAMESPACE_PREFIX, 'middleware' => 'auth'],function(){

    Route::controller(DashboardController::class)->group(function () {
        Route::get('logout','getLogout')->name('manage_logout');
        Route::get('dashboard','getDashboard')->name('dashboard');
        /*Route::get('convert-product-images','convertProductImages');*/
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('setting','index')->name('setting');
        Route::post('store-setting','storeUpdateSeting')->name('update_setting');

        Route::prefix('mobile-slider')->name('mobile_slider.')->group(function(){
            Route::get('','slider')->name('index');
            Route::post('store','storeSlider')->name('store');
            Route::post('delete','deleteSlider')->name('delete');
        });
    });

    //Lookup Types
    Route::controller(TypesController::class)->group(function(){
        Route::prefix('lookup/{type}')->name('lookup.types.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('lookup/{type}')->name('lookup.type.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Lookup Image Types
    Route::controller(ImageTypesController::class)->group(function(){
        Route::prefix('lookup-image/{type}')->name('lookup.image_types.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('lookup-image/{type}')->name('lookup.image_type.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Lookup Sub Types
    Route::controller(SubTypesController::class)->group(function(){
        Route::prefix('lookup/{type}/{subType}')->name('lookup.sub_types.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('lookup/{type}/{subType}')->name('lookup.sub_type.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Lookup Sub Image Types
    Route::controller(SubImageTypesController::class)->group(function(){
        Route::prefix('lookup-image/{type}/{subType}')->name('lookup.sub_image_types.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('lookup-image/{type}/{subType}')->name('lookup.sub_image_type.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Sub Sub Category
    Route::controller(SubSubCategoryController::class)->group(function(){
        Route::prefix('sub-sub-categories')->name('sub_sub_categories.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('sub-sub-category')->name('sub_sub_category.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Size
    Route::controller(SizeController::class)->group(function(){
        Route::prefix('sizes')->name('sizes.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('list');
        });

        Route::prefix('size')->name('size.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','destroy')->name('delete');
        });
    });

    Route::controller(UserController::class)->group(function(){
        Route::prefix('users')->name('users.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('lists');
            Route::post('change-status','changeStatus')->name('change_status');
            Route::post('change-approved','changeApproved')->name('change_approved');
        });
    });

    Route::controller(ProductController::class)->group(function(){
        Route::prefix('products')->name('products.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('lists');
            Route::post('store-popular','storePopularProduct')->name('store_popular');
        });

        Route::prefix('product')->name('product.')->group(function(){
            Route::post('store','store')->name('store');
            Route::post('delete','delete')->name('delete');
            Route::post('change-status','changeStatus')->name('change_status');
            Route::post('store-weight','storeWeight')->name('store_weight');
            Route::post('images','getProductImages')->name('images');
            Route::post('image/store','storeProductImage')->name('image.store');
            Route::post('image/delete','deleteProductImage')->name('image.delete');
        });
    });

    //Change Password
    Route::controller(ProfileController::class)->group(function(){
        Route::prefix('profile')->name('profile.')->group(function(){
            Route::get('change-password','index')->name('change_password');
            Route::post('update-password','storeUpdatePassword')->name('update_password');
        });
    });

    //Order
    Route::controller(OrderController::class)->group(function(){
        Route::prefix('orders')->name('orders.')->group(function(){
            Route::get('/','index')->name('index');
            Route::post('ajax-list','ajaxList')->name('lists');
        });

        Route::prefix('order')->name('order.')->group(function(){
            Route::post('change-status','changeStatus')->name('change_status');
        });
    });

    //Invoice
    Route::controller(InvoiceController::class)->group(function(){
        Route::prefix('invoices')->name('invoices.')->group(function(){
            Route::get('generate-invoice/{id}','generateInvoice')->name('generate_invoice');
            Route::get('generate-short-invoice/{id}','generateInvoice')->name('generate_short_invoice');
            Route::post('generate-catalogue','generateCatalogue')->name('generate_catalogue');
            Route::post('generate-main-catalogue','generateSingleCatalogue')->name('generate_single_catalogue');
        });
    });
});