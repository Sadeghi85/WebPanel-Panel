@extends('layouts.default')
<?php if ( ! defined('VIEW_IS_ALLOWED')) { ob_clean(); die(); } ?>

@section('title')
	@lang('users/messages.edit.title') :: @parent
@stop

@section('style')
@parent
	<style type="text/css">

	</style>
@stop

@section('javascript')
@parent
	<script type="text/javascript">

	</script>
@stop

@section('content')
<div class="page-header">
	<h3>
		@lang('users/messages.edit.header')

		<a href="{{ route('domains.index') }}" class="btn btn-sm btn-primary pull-right"><i class="glyphicon glyphicon-circle-arrow-left"></i> @lang('users/messages.edit.back')</a>
	</h3>
</div>

{{ Form::open(array('route' => array('users.update', $user->id), 'method' => 'PUT', 'class' => '', 'id' => 'form', 'autocomplete' => 'off')) }}

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">General</h3>
	</div>
	<div class="panel-body">
		
		<p>&nbsp;</p>
		
		{{ Form::hidden('indexPage', Input::old('indexPage', (isset($indexPage) ? $indexPage : 1)), array('class'=>'form-control')) }}
		
		<div class="row">
			<div class="col-md-36">
			
				<!-- Username -->
				<div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('username', Lang::get('users/messages.edit.username').' *', array('class' => 'control-label')) }}
								
							</div>

							<div class="col-md-32">
								{{ Form::text('username', Input::old('username', $user->username), array('class'=>'form-control')) }}
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
								{{ Form::label('password', Lang::get('users/messages.edit.password').' *', array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::password('password', array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('password') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Password Confirm -->
				<div class="form-group {{ $errors->has('password_confirm') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('password_confirm', Lang::get('users/messages.edit.password_confirm').' *', array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::password('password_confirm', array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('password_confirm') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- First Name -->
				<div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('first_name', Lang::get('users/messages.edit.first_name'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::text('first_name', Input::old('first_name', $user->first_name), array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('first_name') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Last Name -->
				<div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('last_name', Lang::get('users/messages.edit.last_name'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::text('last_name', Input::old('last_name', $user->last_name), array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('last_name') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Activation Status -->
				<div class="form-group">
					<fieldset class="">
						<div class="row">
							<div class="">
								{{ Form::label('activated', Lang::get('users/messages.edit.activated'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::select('activated', array('0'=>Lang::get('general.no'),'1'=>Lang::get('general.yes')), Input::old('activated', $user->activated), array('class'=>'form-control')) }}
							</div>
						</div>
					</fieldset>
				</div>
			
				<!-- Groups -->
				<div class="form-group {{ $errors->has('group') ? 'has-error' : '' }}">
					<fieldset class="">
						<div class="row">
							<div class="">
								{{ Form::label('group', Lang::get('users/messages.edit.group'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								<select name="group" id="group" class="form-control">
									<option value="-1" {{ ((Input::old('group', $userGroup) < 1) ? 'selected="selected"' : '') }} style="display:none;">@lang('users/messages.edit.select_group')</option>
									
									@foreach ($groups as $group)
										<option value="{{ $group->id }}" {{ ((Input::old('group', $userGroup) == $group->id) ? 'selected="selected"' : '') }}>{{ $group->name }}</option>
									@endforeach
								</select>
								<p class="help-block">{{ $errors->first('group') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
			
				<p class="help-block">Fields with asterisk (*) are required.</p>
			</div>
			
		</div>
		
	</div>
</div>

<!-- Form Actions -->
<div class="form-group">
	<button type="reset" class="btn btn-default">Reset</button>
	<button type="submit" class="btn btn-primary">Edit User</button>
</div>

{{ Form::close() }}

@stop