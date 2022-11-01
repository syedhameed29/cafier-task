@extends('master')
@section('pageheader')
<h2>Add Car</h2>
@stop
@section('maincontent')
<div class="row">
	<div class="col">
		<section class="card">
			<form class="form-horizontal form-bordered" action="{{ route('addcoursespost') }}" method="post" enctype="multipart/form-data">
				@csrf
				<header class="card-header">              
					<a href="{{ route('managecourses') }}" class="btn btn-primary btn-sm pull-right">Manage Car</a>
					<h2 class="card-title">Add Car</h2>
				</header>
				<div class="card-body">

					@if (Session::has('error'))
					<div class="alert alert-danger">{{ Session::get('error') }}</div>
					@endif 

					@if(Session::has('status') && Session::get('status'))
					<div class="row">
						<div class="col">
							<div class="alert alert-success mt-20">
								Car Add  <strong> Succesfully!</strong>
							</div>
						</div>
					</div>
					@endif                 

					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Such As Year <span class="req">*</span></label>
						<div class="col-lg-6">
							<select name="year" class="form-control" id="year">
								<option value="">Select Model Year  </option>
								<option value="2016">2016  </option>
								<option value="2017">2017  </option>
								<option value="2018">2018  </option>
								<option value="2019">2019  </option>
								
							</select>
							@if ($errors->has('year')) 
							<div class="validation-error errorActive">{!! $errors->first('year') !!}</div> 
							@endif
						</div>
					</div>

					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Model</label>
						<div class="col-lg-6">
							<input type="text"  class="form-control" name="model_name" id="model_name">
							@if ($errors->has('model_name')) 
							<div class="validation-error errorActive">{!! $errors->first('model_name') !!}</div> 
							@endif
						</div>
					</div>

					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Color</label>
						<div class="col-lg-6">
							<input type="text"  class="form-control" name="color" id="color">
							@if ($errors->has('color')) 
							<div class="validation-error errorActive">{!! $errors->first('color') !!}</div> 
							@endif
						</div>
					</div>


					

					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Mileage</label>
						<div class="col-lg-6">
							<input type="text"  class="form-control" name="mileage" id="mileage">
							@if ($errors->has('mileage')) 
							<div class="validation-error errorActive">{!! $errors->first('mileage') !!}</div> 
							@endif
						</div>
					</div>


					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">File Upload</label>
						<div class="col-lg-6">
							<input type="file" name="file" class="form-control">
							@if ($errors->has('file')) 
							<div class="validation-error errorActive">{!! $errors->first('file') !!}</div> 
							@endif
						</div>
					</div>
				
				</div>
				<footer class="card-footer text-right">
					<button class="btn btn-primary btn-sm">Submit </button>
					<button type="reset" class="btn btn-default btn-sm">Reset</button>
				</footer>
			</form>
		</section>
	</div>
</div>
@stop

@section('javascript')
<script type="text/javascript">

	$(function() {
		$('#courseselection').change(function(){
			var selected_option = $('#courseselection').val();
			console.log(selected_option);
			if(selected_option == 'Free'){
				$(".courseprice").hide();
				
			} else {
            $(".courseprice").show();
        }

		});
	});



</script>
@stop