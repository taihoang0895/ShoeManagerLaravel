<?php


namespace App\Http\Middleware;


use App\models\functions\Log;
use App\User;
use http\Env\Request;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = array(
            "status" => 302,
            "content" => "",
            "message" => "Permission Denied"
        );

        if (Auth::check()) {
            if(Auth::user()->isAdmin()){
                $request->session()->put("current_department_name", "Admin");
                if($request->is('sale/*', 'sale')){
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName(User::$DEPARTMENT_SALE));
                }
                if($request->is('marketing/*', 'marketing')){
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName(User::$DEPARTMENT_MARKETING));
                }
                if($request->is('storekeeper/*', 'storekeeper')){
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER));
                }

                return $next($request);
            }
            if(Auth::user()->isSale()){
                if(Auth::user()->isSaleAdmin()){
                    if(!$request->is('sale/*', 'sale') && !$request->is('storekeeper/*', 'storekeeper')){
                        return response()->json($response);
                    }else{
                        if($request->is('sale/*', 'sale')){
                            $request->session()->put("current_department_name", User::convertCodeToDepartmentName(User::$DEPARTMENT_SALE));
                        }
                        if($request->is('storekeeper/*', 'storekeeper')){
                            $request->session()->put("current_department_name", User::convertCodeToDepartmentName(User::$DEPARTMENT_STOREKEEPER));
                        }
                    }
                }else{
                    if(!$request->is('sale/*', 'sale')){
                        return response()->json($response);
                    }
                }

            }
            if(Auth::user()->isMarketing()){
                if(!$request->is('marketing/*', 'marketing')){
                    return response()->json($response);
                }
            }
            if(Auth::user()->isStoreKeeper()){
                if(!$request->is('storekeeper/*', 'storekeeper')){
                    return response()->json($response);
                }
            }
        } else {
            return response()->json($response);
        }

        return $next($request);
    }

}
