<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Company;
use App\Employee;
use App\User;
use App\Attendance;
use App\FTP;
use Validator;
use Log;

class EmployeeController extends Controller
{
    public function searchEmployee($phone) {
    	try {
    		$company = Company::where('user_id', auth('api')->id())->first();
	    	if(!$company) {
	    		return response()->json(['success'=>false, 'message'=>'Unauthorized Access'], 401);
	    	}
	    	$employee = Employee::where('company_id', $company->id)->where('phone', $phone)->first();

	    	if($employee) {
                $user = User::find($employee->user_id);
                $employee->info = $user;
                return response()->json(['success' => true, 'data' => $employee]);
            } else {
	    	    return response()->json(['success' => false, 'message'=>'Employee not found']);
            }
    	} catch(\Exception $e) {
    		return response()->json(['success'=>false, 'message' => $e->getMessage()]);
    	}
    }

    public function timesheet(Request $request) {
    	try {
    		$company = Company::where('user_id', auth('api')->id())->first();
	    	if($company) {
	    		if (isset($request->from) || isset($request->to)) {
	    			$rules = [
	    				'from'    => 'required|date',
	    				'to'      => 'required|date|after_or_equal:from',
	    			];

	    			$messages = [
	    				'from.required' => 'Select start date',
	    				'to.required' => 'Select end date',
	    				'from.date' => 'Date is invalid',
	    				'to.date' => 'Date is invalid',
	    				'after_or_equal' => 'End date cannot be lesser than Start date'
	    			];

	    			$validation = Validator::make($request->all(), $rules, $messages);

	    			if($validation->fails()){
	    				$validation = collect($validation->messages())->flatten()->first();
	    				return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
	    			}
	    		}
	    		$result = [];
	    		$list = Attendance::with('employee.user')
	    			->where('company_id', $company->id);
	    			if (isset($request->from) && isset($request->to)) {
	    				$list = $list->whereBetween('attendance_date', [date('Y-m-d', strtotime($request->from)), date('Y-m-d', strtotime($request->to))]);
	    			}else{
	    				$list = $list->where('attendance_date', date('Y-m-d'));
	    			}
	    			$list = $list->orderBy('attendance_date', 'DESC')
                        ->orderBy('attendance_time', 'DESC')->get();
	    		foreach($list as $l) {
	    			array_push($result, [
	    				'name' => $l->employee->user->first_name.' '.$l->employee->user->last_name,
	    				'phone' => $l->employee->phone,
	    				'date' => $l->attendance_date,
	    				'time' => $l->attendance_time,
	    				'type' => $l->attendance_status
	    			]);
	    		}
	    		return response()->json(['success'=>true, 'data'=>$result]);
	    	} else {
	    		return response()->json(['success'=>false, 'message'=>'Unauthorized'], 401);
	    	}
    	} catch(\Exception $e) {
    		Log::error('Create employee error: '.$e->getMessage().' '.$e->getLine());
    		return response()->json(['success'=>false, 'Create employee error: ' => $e->getMessage().' '.$e->getLine()]);
    	}
    }

    public function employeeTimeSheet(Request $request) {
    	try {
    		$employee = Employee::where('user_id', auth('api')->id())->first();
	    	if($employee) {
	    		if (isset($request->from) || isset($request->to)) {
	    			$rules = [
	    				'from'    => 'required|date',
	    				'to'      => 'required|date|after_or_equal:from',
	    			];

	    			$messages = [
	    				'from.required' => 'Select start date',
	    				'to.required' => 'Select end date',
	    				'from.date' => 'Date is invalid',
	    				'to.date' => 'Date is invalid',
	    				'after_or_equal' => 'End date cannot be lesser than Start date'
	    			];

	    			$validation = Validator::make($request->all(), $rules, $messages);

	    			if($validation->fails()){
	    				$validation = collect($validation->messages())->flatten()->first();
	    				return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
	    			}
	    		}
	    		$result = [];
	    		$list = Attendance::where('employee_id', $employee->id);
	    			if (isset($request->from) && isset($request->to)) {
	    				$list = $list->whereBetween('attendance_date', [date('Y-m-d', strtotime($request->from)), date('Y-m-d', strtotime($request->to))]);
	    			}else{
	    				$list = $list->where('attendance_date', date('Y-m-d'));
	    			}
	    			$list = $list->orderBy('attendance_date', 'DESC')->orderBy('attendance_time', 'DESC')->get();
	    		foreach($list as $l) {
	    			array_push($result, [
	    				'date' => $l->attendance_date,
	    				'time' => gmdate('h:i A', $l->attendance_time),
	    				'type' => $l->attendance_status
	    			]);
	    		}
	    		return response()->json(['success'=>true, 'data'=>$result]);
	    	} else {
	    		return response()->json(['success'=>false, 'message'=>'Unauthorized'], 401);
	    	}
    	} catch(\Exception $e) {
    		Log::error('Create employee error: '.$e->getMessage().' '.$e->getLine());
    		return response()->json(['success'=>false]);
    	}
    }

    public function ftpSave(Request $request)
    {
    	try {
    		$company = Company::where('user_id', auth('api')->id())->first();
	    	if($company) {
    			$rules = [
    				'username' => 'required',
    				'password' => 'required',
    				'path' => 'required'
    			];
    			$validation = Validator::make($request->all(), $rules);
    			if($validation->fails()){
    				$validation = collect($validation->messages())->flatten()->first();
    				return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
    			}
	    		$ftp = FTP::where('company_id', $company->id)->first();
	    		if ($ftp) {
	    			$ftp = FTP::where('company_id', $company->id)->update([
	                    'username' => $request->username,
	                    'password' => $request->password,
	                    'path' => $request->path
	                ]);
	    		}else{
		    		$ftp = FTP::create([
	                    'company_id' => $company->id,
	                    'username' => $request->username,
	                    'password' => $request->password,
	                    'path' => $request->path
	                ]);
	    		}
	    		return response()->json(['success' => true]);
	    	} else {
	    		return response()->json(['success'=>false, 'message'=>'Unauthorized'], 401);
	    	}
    	} catch(\Exception $e) {
    		Log::error('Create employee error: '.$e->getMessage().' '.$e->getLine());
    		return response()->json(['success'=>false, 'Create employee error: ' => $e->getMessage().' '.$e->getLine()]);
    	}
    }

    public function ftpRetrieve(Request $request)
    {
    	try {
    		$company = Company::where('user_id', auth('api')->id())->first();
	    	if($company) {
	    		$ftp = FTP::where('company_id', $company->id)->first();
	    		return response()->json(['success' => true, 'ftp_info' => $ftp]);
	    	} else {
	    		return response()->json(['success'=>false, 'message'=>'Unauthorized'], 401);
	    	}
    	} catch(\Exception $e) {
    		Log::error('Create employee error: '.$e->getMessage().' '.$e->getLine());
    		return response()->json(['success'=>false, 'Create employee error: ' => $e->getMessage().' '.$e->getLine()]);
    	}
    }
}
