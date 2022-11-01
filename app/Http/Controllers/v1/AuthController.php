<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Log;
use Validator;
use DB;
use Hash;

use App\User;
use App\Company;
use App\Employee;
use App\Attendance;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','signup']]);
    }

    public function signup(Request $request) {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'name' => 'required|unique:companies|max:100',
            'address' => 'required|max:255',
            'phone' => 'required|unique:companies|numeric|min:1000000000|max:9999999999',
            'email' => 'required|email|unique:companies'
        ];

        $messages = [
            'phone.min' => 'Your phone number is invalid',
            'phone.max' => 'Your phone number is invalid'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()) {
            $validation = collect($validation->messages())->flatten()->first();
            return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
        }

        try {
            DB::transaction(function() use($request) {
                $create = Company::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email
                ]);

                $password = User::generateRandomString(8);
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'username' => $request->phone,
                    'password' => Hash::make($password),
                    'user_type' => 'company'
                ]);

                $update = Company::find($create->id);
                $update->user_id = $user->id;
                $update->save();
            });
            return response()->json(['success'=>true]);
        } catch(\Exception $e) {
            Log::error('Signup Error: '.$e->getMessage());
            return response()->json(['success'=>false]);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);
        try {
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);
        } catch(\Exception $e) {
            Log::error("Auth error: ".$e->getMessage().' at '.$e->getLine());
            return response()->json(['success' => false, 'message' => 'Server error '.$e->getMessage()]);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['success'=>true, 'message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $info = User::where('id', auth('api')->id())->with('employee')->first();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'success' => true,
            'name' => $info->first_name.' '.$info->last_name,
            'user_type' => $info->user_type,
            'user_id' => auth('api')->id()
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    public function createEmployee(Request $request) {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'phone' => 'required|unique:employees|numeric|min:10000000|max:9999999999',
            'email' => 'required|email|unique:employees'
        ];

        $messages = [
            'phone.min' => 'The phone number is invalid',
            'phone.max' => 'The phone number is invalid'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()) {
            $validation = collect($validation->messages())->flatten()->first();
            return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
        }

        try {

            $company = Company::where('user_id', auth('api')->id())->first();

            if(!$company) {
                Log::error('Company not found in create employee: ');
                return response()->json(['success'=>false]);
            }

            DB::transaction(function() use($request, $company, &$update) {
                $create = Employee::create([
                    'company_id' => $company->id,
                    'phone' => $request->phone,
                    'email' => $request->email
                ]);

                Log::info('Created: '.$create->id);

                //$password = User::generateRandomString(8);
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'username' => $request->phone,
                    'password' => Hash::make('pass1234'),
                    'user_type' => 'employee'
                ]);

                $update = Employee::find($create->id);
                $update->company_id = $company->id;
                $update->user_id = $user->id;
                $update->save();
            });
            return response()->json(['success'=>true, 'v'=>$update]);
        } catch(\Exception $e) {
            Log::error('Create employee error: '.$e->getMessage().' '.$e->getLine());
            return response()->json(['success'=>false, 'message'=>$e->getMessage()]);
        }
    }

    public function getEmployeeDetails(Request $request)
    {
        try {
            $employee = Employee::where('user_id', auth('api')->id())->first();
            if($employee) {
                $info = User::where('id', auth('api')->id())->with('employee')->first();

                $result = [];
                $list = Attendance::where('employee_id', $info->employee->id)
                    ->where('attendance_date', date('Y-m-d'))
                    ->orderBy('attendance_date', 'DESC')
                    ->orderBy('attendance_time', 'DESC')
                    ->get();
                foreach($list as $l) {
                    array_push($result, [
                        'date' => $l->attendance_date,
                        'time' => $l->attendance_time,
                        'type' => $l->attendance_status
                    ]);
                }
                if ($info) {
                    return response()->json(['success' => true, 'data' => $info, 'attendance_date' => $result]);
                }
                return response()->json(['success' => false, 'data' => 'Something went to wrong']);
            } else {
                return response()->json(['success'=>false, 'message'=>'Unauthorized'], 401);
            }

        } catch (Exception $e) {
            Log::error("Auth error: ".$e->getMessage().' at '.$e->getLine());
            return response()->json(['success' => false, 'data' => 'Server Error!']);
        }
    }

}
