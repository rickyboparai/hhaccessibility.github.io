@extends('layouts.default')
@section('head-content')
  <link href="/css/jquery/jquery-ui.css" rel="stylesheet" type="text/css"> 
  <script src="/css/jquery/external/jquery/jquery.js"></script>
  <script src="/css/jquery/jquery-ui.js"></script>
  <script src="/js/profile.js"></script>
  <link href="/css/app.css" rel="stylesheet" type="text/css">
@stop
@section('content')
<div class="profile row">
	<div class="col-md-3 col-sm-4 col-xs-12">
		@if ($has_profile_photo)
			<div class="photo-display" onclick="selectImageFile()">
				<div class="uploaded-photo">
				</div>
                <p>Change Photo</p>
			</div>
		@else
        
        <div class="photo-display" onclick="selectImageFile()">
            <div class="user-icon"> 
                <div><i class="fa fa-user"></i></div>
                <p>Choose File</p>
            </div>
        </div>
        @endif        	
		<form id="photo-upload" method="post" action="/profile-photo-upload" enctype="multipart/form-data">
			{!! csrf_field() !!}
            <input class="hidden-uploader" type="file" name="profile_photo" onchange="upload()">
		</form>
 	</div>
    <div class="col-md-9 col-sm-8 col-xs-12">
        <h1>{{ $user->first_name.' '.$user->last_name }}</h1>

		<p>Some elements of this page are under development</p>
		<form method="post" action="/profile">
			{!! csrf_field() !!}
			@include('pages.validation_messages', array('errors'=>$errors))
			<h2>Personal</h2>
			<div class="box">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="first_name">First Name</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="last_name">Last Name</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="email">Email</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="email" name="email" type="email" required readonly value="{{ $user->email }}">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label>Password</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<a class="btn btn-default" href="/change-password">Change password</a>
						</div>
					</div>
				</div>
			</div>
			
			<h2>Home</h2>
			<div class="box">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="home_country_id">Country</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<select class="form-control" id="home_country_id" name="home_country_id">
								<option value="">-- Select Country --</option>
								@foreach ($countries as $country)
									@if ($user->home_country_id === $country->id)
									<option value="{{ $country->id }}" selected>{{ $country->name }}</option>
									@else
									<option value="{{ $country->id }}">{{ $country->name }}</option>
									@endif
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="province">State/Province</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="province" name="province" value="{{ $user->home_region }}">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="home_city">City</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="home_city" name="home_city" value="{{ $user->home_city }}">
						</div>
					</div>
				</div>
			</div>
			
			<h2>Search Location</h2>
			<div class="box">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="location_search_text">Location</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<input class="form-control" id="location_search_text" name="location_search_text"
								value="{{ $user->location_search_text }}"
								placeholder="{{ $address_default }}">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4 col-xs-5">
							<label for="distance">Distance (km)</label>
						</div>
						<div class="col-sm-8 col-xs-7">
							<!-- from 10 meters to the full radius of Earth -->
							<input class="form-control" id="distance" name="search_radius_km"
								type="number"
								step="0.01"
								min="0.01"
								max="20040"
								value="{{ $user->search_radius_km }}">
						</div>
					</div>
				</div>
			</div>

			<h2>My Accessibility Requirements </h2>
			<div class="box accessibility-interests">
				<div class="checkbox">
					<label>
					@if ( $user->uses_screen_reader )
					<input type="checkbox" name="uses_screen_reader" checked>
					@else
					<input type="checkbox" name="uses_screen_reader">
					@endif
					Screen Reader</label>
				</div>
				<div id="accordion">
			
				@foreach ($question_categories as $category)
			
					<h3>{{ $category->name }}</h3>
					<div class="category">
						<div class="checkbox">
						  <label><input class="select-all" type="checkbox" value="">Select All</label>
						</div>
						<div class="questions">
							@foreach ($category->questions()->get() as $question)
								<div class="checkbox">
								  <label>
									@if ($user->isQuestionRequired($required_questions, $question->id))
									<input name="question_{{ $question->id }}" type="checkbox" checked>
									@else
									<input name="question_{{ $question->id }}" type="checkbox">
									@endif
									</label>
									<div>
									{!! $question->question_html !!}
									</div>
								</div>
							@endforeach
						</div>
					</div>
					
				@endforeach

				</div>
				
			</div>
			
			<h2>Reward Program (Under Development)</h2>
			<div class="box rewards">
				<p>My Rewards:</p>
				<div>
					 <i class="fa fa-trophy fa-3x"></i> <i class="fa fa-trophy fa-3x"></i>
				</div>
				<div>
					<a class="btn btn-default" href="/reviewed-locations">My Reviews({{ $num_reviews }})</a>
				</div>
			</div>

			<div class="text-right">
				<button type="submit" class="btn btn-lg btn-primary save-button"><i class="fa fa-check"></i> Save Profile</button>
		   </div>
	 </form>
	 
	</div>
</div>

@stop