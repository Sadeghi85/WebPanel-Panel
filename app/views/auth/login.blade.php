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
		
			{{ Form::open(array('route' => 'auth.login', 'class' => 'box', 'autocomplete' => 'off')) }}

			<div class="row">
				<div class="col-md-offset-2 col-md-70">

					<legend><h3>{{ Lang::get('auth/messages.login.formname') }}</h3></legend>
					<p>&nbsp;</p>
					
					<fieldset>
						
						<!-- Username -->
						<div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
							<fieldset class="form-inline">
								<div class="row">
									<div class="">
										<label class="control-label" for="username">{{ Lang::get('auth/messages.login.username') }}</label>
									</div>

									<div class="col-md-32">
										{{ Form::text('username', Input::old('username'), array('class'=>'form-control')) }}
										<p class="help-block">{{ $errors->first('username') }}</p>
									</div>

								</div>
							</fieldset>
						</div>

						<!-- Password -->
						<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
							<fieldset class="form-inline">
								<div class="row">
									<div class="">
										<label class="control-label" for="password">{{ Lang::get('auth/messages.login.password') }}</label>
									</div>

									<div class="col-md-32">
										{{ Form::password('password', array('class'=>'form-control')) }}
										<p class="help-block">{{ $errors->first('password') }}</p>
									</div>

								</div>
							</fieldset>
						</div>

						<!-- Remember me -->
						<div class="form-group">
							<fieldset class="form-inline">
								<div class="row">
									<div class="">
										<label class="control-label">&nbsp;</label>
									</div>
									
									<div class="col-md-32">
										<label class="checkbox inline">
											{{ Form::checkbox('remember-me', 'remember-me') }}
											{{ Lang::get('auth/messages.login.remember-me') }}
										</label>
									</div>
								</div>
							</fieldset>
						</div>

						<p>&nbsp;</p>

						<!-- Login button -->
						<div class="form-group">
							<fieldset class="form-inline">
								<div class="row">
									<div class="">
										<label class="control-label">&nbsp;</label>
									</div>
									
									<div class="col-md-32">
										{{ Form::submit(Lang::get('auth/messages.login.login'), array('class' => 'btn btn-lg btn-default')) }}
									</div>
								</div>
							</fieldset>
						</div>
					</fieldset>
				</div>
			</div>
			
			{{ Form::close() }}
			
		</div>
	</div>
</div>
@stop