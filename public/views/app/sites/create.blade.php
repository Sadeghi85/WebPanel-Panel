@extends('layouts.default')
<?php if ( ! defined('VIEW_IS_ALLOWED')) { ob_clean(); die(); } ?>

@section('title')
	@lang('sites/messages.create.title') :: @parent
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
		@lang('sites/messages.create.header')

		<a href="{{ route('sites.index') }}" class="btn btn-sm btn-primary pull-right"><i class="glyphicon glyphicon-circle-arrow-left"></i> @lang('sites/messages.create.back')</a>
	</h3>
</div>

{{ Form::open(array('route' => 'sites.store', 'method' => 'POST', 'class' => '', 'id' => 'form', 'autocomplete' => 'off')) }}

	{{ Form::hidden('indexPage', Input::old('indexPage', (isset($indexPage) ? $indexPage : 1)), array('class'=>'form-control')) }}
	
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">General</h3>
	</div>
	<div class="panel-body">
		
		<p>&nbsp;</p>
		
		<div class="row">
			<div class="col-md-36">
			
				<!-- Port -->
				<div class="form-group {{ $errors->has('port') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('port', Lang::get('sites/messages.create.port').' *', array('class' => 'control-label')) }}
							</div>

							<div class="col-md-36">
								<div class="row">
									<div class="col-md-24">
										{{ Form::text('port', Input::old('port', '80'), array('class'=>'form-control')) }}
									</div>
								</div>
								<div class="row">
									<div class="col-md-72">
										<p class="help-block">{{ $errors->first('port') }}</p>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- ServerName -->
				<div class="form-group {{ $errors->has('server_name') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('server_name', Lang::get('sites/messages.create.server_name').' *', array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::text('server_name', Input::old('server_name'), array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('server_name') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Aliases -->
				<div class="form-group {{ $errors->has('aliases') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('aliases', Lang::get('sites/messages.create.aliases'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::textarea('aliases', Input::old('aliases'), array('class'=>'form-control', 'rows'=>'10')) }}
								<p class="help-block">{{ $errors->first('aliases') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Quota -->
				<div class="form-group {{ $errors->has('quota') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('quota', Lang::get('sites/messages.create.quota'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-36">
								<div class="row">
									<div class="col-md-24">
										{{ Form::text('quota', Input::old('quota', '0'), array('class'=>'form-control')) }}
									</div>
								</div>
								<div class="row">
									<div class="col-md-72">
										<p class="help-block">{{ $errors->first('quota') }}</p>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Activation Status -->
				<div class="form-group">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('activate', Lang::get('sites/messages.create.activate'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-36">
								<div class="row">
									<div class="col-md-24">
										{{ Form::select('activate', array('0'=>Lang::get('general.no'),'1'=>Lang::get('general.yes')), Input::old('activate', 0), array('class'=>'form-control')) }}
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			
				<p class="help-block">Fields with asterisk (*) are required.</p>
			</div>
			
		</div>

	</div>
</div>

@if (Group::isRoot())
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Users</h3>
		</div>
		<div class="panel-body">
			
			<p>&nbsp;</p>
			
			<div class="row">
				<div class="col-md-36">
					<!-- Users -->
					<div class="form-group {{ $errors->has('users') ? 'has-error' : '' }}">
						<fieldset class="">
							<div class="row">
								<div class="">
									{{ Form::label('users', Lang::get('sites/messages.create.users'), array('class' => 'control-label')) }}
								</div>

								<div class="col-md-32">
									<select name="users[]" id="users" multiple="multiple" class="form-control">
										@foreach ($users as $user)
											<option value="{{ $user->id }}"{{ (in_array($user->id, $selectedUsers) ? ' selected="selected"' : '') }}>{{ $user->username }}</option>
										@endforeach
									</select>
									<p class="help-block">{{ $errors->first('users') }}</p>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif

<!-- Form Actions -->
<div class="form-group">
	<button type="reset" class="btn btn-default">Reset</button>
	<button type="submit" class="btn btn-primary">Create Site</button>
</div>

{{ Form::close() }}

@stop