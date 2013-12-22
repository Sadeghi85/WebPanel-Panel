@extends('layouts.default')

@section('title')
	{{ Lang::get('auth/messages.login.title') }} | @parent
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

			<p>&nbsp;</p>
			
			<!-- Username -->
			<div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
				{{ Form::text('username', Input::old('username'), array('class'=>'form-control', 'placeholder' => Lang::get('auth/messages.login.username'))) }}
				<p class="help-block">{{ $errors->first('username') }}</p>
			</div>

			<!-- Password -->
			<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
				{{ Form::password('password', array('class'=>'form-control', 'placeholder' => Lang::get('auth/messages.login.password'))) }}
				<p class="help-block">{{ $errors->first('password') }}</p>
			</div>

			<!-- Remember me -->
			<div class="form-group">
				<label class="checkbox">
					{{ Form::checkbox('remember-me', 'remember-me') }}
					{{ Lang::get('auth/messages.login.remember-me') }}
				</label>
			</div>

			<p>&nbsp;</p>

			<!-- Login button -->
			<div class="form-group">
				{{ Form::submit(Lang::get('auth/messages.login.login'), array('class' => 'btn btn-lg btn-default')) }}
			</div>

		{{ Form::close() }}

			</div>
		</div>

		</div>
	</div>
</div>
@stop