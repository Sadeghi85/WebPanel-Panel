@extends('layouts.default')

@section('title')
	{{ Lang::get('title.login') }} | @parent
@stop

@section('style')
<style type="text/css">

</style>
@stop

@section('navbar')

@stop

@section('container')
<div class="container">
	<div class="row">
      <div class="col-md-offset-18 col-md-36">
		{{ Form::open(array('route' => 'auth.login', 'class' => 'box')) }}

		<div class="row">
      		<div class="col-md-offset-12 col-md-48">
			<h3>{{Lang::get('site.login-message')}}</h3>

			<p>&nbsp;</p>

			<!-- Message -->
			<div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
				{{ Form::label('message', ($errors->first('message') ? $errors->first('message') : ' '), array('class'=>'control-label')) }}
			</div>

			<!-- Username -->
			<div class="form-group">
				{{ Form::text('username', Input::old('username'), array('class'=>'form-control', 'placeholder' => Lang::get('site.username'))) }}
			</div>

			<!-- Password -->
			<div class="form-group">
				{{ Form::password('password', array('class'=>'form-control', 'placeholder' => Lang::get('site.password'))) }}
			</div>

			<!-- Remember me -->
			<div class="form-group">
				<label class="checkbox">
					{{ Form::checkbox('remember-me', 'remember-me') }}
					{{ Lang::get('site.remember-me') }}
				</label>
			</div>

			<p>&nbsp;</p>

			<!-- Login button -->
			<div class="form-group">
				{{ Form::submit(Lang::get('site.login'), array('class' => 'btn btn-lg btn-default')) }}
			</div>

		{{ Form::close() }}

			</div>
		</div>

		</div>
	</div>
</div>
@stop