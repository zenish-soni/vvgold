<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\TokenGuard;

class ApiAuthenticate
{   
    protected $auth;
    
    public function __construct(Request $request, AuthManager $auth)
    {
        $this->HeaderSecKey = 'Authorization';
        $this->auth = $auth;
    }
    
    public function handle(Request $request, Closure $next, $guard = 'api') 
    {
        if($this->auth->guard('api')->user())
        {   
            $isValid = false;
            if($request->path() == 'api/v1/store-profile' || $request->path() == 'api/v1/logout' || $request->path() == 'api/v1/check-approval' || $this->auth->guard('api')->user()->is_approved == 2){
                $isValid = true;
            }
            
            if($isValid){
                return $next($request);    
            }
            
        }
        return response(['IsSuccess' => false,'Message' => UNAUTHORIZED_MESSAGE,'ErrorCode' => HTTP_UNAUTHORIZED],HTTP_SUCCESS);
    }
}