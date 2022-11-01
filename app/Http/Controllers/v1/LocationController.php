<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Log;
use Exception;
use Auth;
use Validator;

use App\User;
use App\Attendance;
use App\Company;

class LocationController extends Controller
{
    public function updateCompanyLocation(Request $request) {
        $rules = [
            'address' => 'required|max:255',
            'latitude' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'radius' => 'required|numeric|max:2000'
        ];

        $messages = [
            'latitude.regex' => 'Latitude is invalid',
            'longitude.regex' => 'Longitude is invalid',
            'radius.max' => 'Radius cannot be greater than 2000 meters'
        ];
        
        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()) {
            $validation = collect($validation->messages())->flatten()->first();
            return response()->json(['success' => false, 'type' => 'validation', 'message' => $validation]);
        }

        try {

            $company = Company::where('user_id', auth('api')->id())->first();
            $company->address = $request->address;
            $company->latitude = $request->latitude;
            $company->longitude = $request->longitude;
            $company->radius = $request->radius;
            $company->save();

            return response()->json(['success'=>true]);
        } catch(\Exception $e) {            
            Log::error('Update company location error: '.$e->getMessage().' '.$e->getLine());
            return response()->json(['success'=>false]);
        }
    }

    public function checkBounds(Request $request) {
        Log::info('Received location: ',$request->all());
        try {
            $info = User::where('id', $request['user_id'])->with('employee')->first();
            if ($info) {
                $companyInfo = Company::find($info->employee->company_id); 
                $lat = $request['location']['coords']['latitude'];
                $lng = $request['location']['coords']['longitude'];

                $lat1 = $companyInfo->latitude;
                $lng1 = $companyInfo->longitude;
                $radius = $companyInfo->radius;

                $distance = $this->checkRadius($lat, $lng, $lat1, $lng1);

                Log::info('Distance: '.$distance);
                $attendanceStatus = $radius > $distance ? 'in' : 'out';

                $lastEntry = Attendance::where('employee_id', $info->employee->id)
                                ->where('company_id', $companyInfo->id)
                                ->orderBy('id', 'DESC')
                                ->first();
                if($lastEntry) {
                    if($lastEntry->attendance_status == $attendanceStatus) {
                        return response()->json(['success'=>false, 'message'=>'Duplicate']);
                    }
                }

                $attendance = new Attendance();
                $attendance->employee_id = $info->employee->id;
                $attendance->company_id = $companyInfo->id;
                $attendance->attendance_date = date('Y-m-d');
                $attendance->attendance_time = strtotime(date('H:i:s')) - strtotime(date('Y-m-d'));
                $attendance->attendance_status = $attendanceStatus;
                $attendance->save();

                $result = ['status' => true, 'd'=>$distance, 'status'=>$attendanceStatus];
                
                return response()->json($result);
            }
            return response()->json(['status' => false]);
        } catch (Exception $e) {
            Log::error('App error: '.$e->getMessage().' at Line '.$e->getLine());
            return response()->json(['status' => false, 'message'=>'Unable to save attendance']);
        }
    }

    public function checkRadius($latitude1, $longitude1, $latitude2, $longitude2) {
        $earth_radius = 6371;     
        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);     
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;     
        return $d*1000;        
    }

}
