<?php


namespace App\Http\Middleware;


use App\models\functions\Log;
use App\User;
use http\Env\Request;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
            if (Auth::user()->isAdmin()) {
                if ($request->is('admin/*', 'admin')) {
                    $request->session()->put("current_department_name", "Admin");
                    $request->session()->put("current_department_code", "Admin");
                }
                if ($request->is('sale/*', 'sale')) {
                    $departmentCode = User::$DEPARTMENT_SALE;
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                    $request->session()->put("current_department_code", $departmentCode);
                }
                if ($request->is('marketing/*', 'marketing')) {
                    $departmentCode = User::$DEPARTMENT_MARKETING;
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                    $request->session()->put("current_department_code", $departmentCode);
                }
                if ($request->is('storekeeper/*', 'storekeeper')) {
                    $departmentCode = $request->get("department_code", -1);
                    if($departmentCode == -1){
                        if(Session::has("current_department_code")){
                            Log::log("taih", "current_department_code".  Session::get("current_department_code") );
                            $departmentCode = Session::get("current_department_code");
                        }
                    }
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                    $request->session()->put("current_department_code", $departmentCode);
                }

                return $next($request);
            }
            if (Auth::user()->isSale()) {
                if (Auth::user()->isSaleAdmin()) {
                    if (!$request->is('sale/*', 'sale') && !$request->is('storekeeper/*', 'storekeeper')) {
                        return response()->json($response);
                    } else {
                        if ($request->is('sale/*', 'sale')) {
                            $departmentCode = User::$DEPARTMENT_SALE;
                            $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                            $request->session()->put("current_department_code", $departmentCode);
                        }
                        if ($request->is('storekeeper/*', 'storekeeper')) {
                            $departmentCode = $request->get("department_code", -1);
                            if($departmentCode == -1){
                                if(Session::has("current_department_code")){
                                    $departmentCode = Session::get("current_department_code");
                                }
                            }
                            $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                            $request->session()->put("current_department_code", $departmentCode);
                        }
                    }
                } else {
                    if (!$request->is('sale/*', 'sale')) {
                        return response()->json($response);
                    }
                }

            }
            if (Auth::user()->isMarketing()) {
                if (!$request->is('marketing/*', 'marketing')) {
                    return response()->json($response);
                }else{
                    $departmentCode = Auth::user()->department;
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                    $request->session()->put("current_department_code", $departmentCode);
                }
            }
            if (Auth::user()->isStoreKeeper()) {
                if (!$request->is('storekeeper/*', 'storekeeper')) {
                    return response()->json($response);
                }else{
                    $departmentCode = Auth::user()->department;
                    $request->session()->put("current_department_name", User::convertCodeToDepartmentName($departmentCode));
                    $request->session()->put("current_department_code", $departmentCode);
                }
            }
        } else {
            return response()->json($response);
        }

        return $next($request);
    }

}
