<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Courses;
use Validator;
use Log;
use Exception;
use Auth;

class CoursesController extends Controller
{

	public function addcourses()
	{
		try{
			return view('courses/addcourses');
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}

	public function addcoursespost(Request $req){

		try{

			$input = $req->all();
			$rules = ["year" => "required","model_name" => "required"];
			$validation = Validator::make($input, $rules);
			if($validation->fails()){
				return redirect()->back()->withErrors($validation);
			}

			$fileurl = '';
			if ($req->hasFile('file')) {
				$files = $req->file('file');
				$destinationPath = public_path('uploads');
				$profileImage = date('YmdHis'). "." .$files->getClientOriginalExtension();
				$files->move($destinationPath, $profileImage);
				$fileurl = '/uploads/'.$profileImage;
			}	 

			$query = new Courses();
			$query->year = $req['year'];
			$query->model_name  = $req['model_name'];
			$query->color = $req['color'];
			$query->mileage = $req['mileage'];
			$query->file_upload = $fileurl;
			$query->status = 'active';
			$query->save();
			return redirect()->back()->with(['status'=>true]);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}

	public function managecourses(){


		try{
		    $auth = Auth::id();
		    $user = User::where('id',$auth)->first();
			$records = Courses::where('status','!=','deleted')->get();
			return view('courses/managecourses')->with('records',$records)->with('user',$user);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}


	public function statuscourses(Request $req){
		try{
			$id = $req["id"];
			$company = Courses::find($id);
			$previous = $company->status;
			$company->status == "active" ? $current = "inactive" : $current = "active";
			$company->status = $current;
			$company->save();
			return response()->json(["success"=>true,'status'=>$current]);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}
	public function deletecourses(Request $req){
		try{
			$id = $req["id"];
			$company = Courses::find($id);
			$company->status = "deleted";
			$company->save();
			return response()->json(["status"=>'success']);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}

	public function editcourses($id){
		try{
			$query = Courses::where('id',$id)->first();
			return view('Courses/editcourses')->with('query',$query);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}
	}


	public function editcoursespost(Request $req){
		try{
			$data =  $req->all();
			$fileurl = '';
			if ($req->hasFile('file')) {
				$files = $req->file('file');
				$destinationPath = public_path('uploads');
				$profileImage = date('YmdHis'). "." .$files->getClientOriginalExtension();
				$files->move($destinationPath, $profileImage);
				$fileurl = '/uploads/'.$profileImage;
			}
			$update = Courses::find($data['editid']);
			$update->year = $req['year'];
			$update->model_name  = $req['model_name'];
			$update->color = $req['color'];
			$update->mileage = $req['mileage'];
			$update->file_upload = $fileurl;
			$update->status = 'active';
			$update->save();	 
			return redirect()->back()->with(['status'=>true]);
		}catch (Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
		}

	}
}