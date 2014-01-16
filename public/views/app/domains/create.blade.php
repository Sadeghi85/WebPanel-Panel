@extends('layouts.default')
<?php if ( ! defined('VIEW_IS_ALLOWED')) { ob_clean(); die(); } ?>

@section('title')
	@lang('domains/messages.create.title') :: @parent
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
		@lang('domains/messages.create.header')

		<a href="{{ route('domains.index') }}" class="btn btn-sm btn-primary pull-right"><i class="glyphicon glyphicon-circle-arrow-left"></i> @lang('domains/messages.create.back')</a>
	</h3>
</div>

{{ Form::open(array('route' => 'domains.store', 'method' => 'POST', 'class' => '', 'id' => 'form', 'autocomplete' => 'off')) }}

	{{ Form::hidden('indexPage', Input::old('indexPage', (isset($indexPage) ? $indexPage : 1)), array('class'=>'form-control')) }}
	
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">General</h3>
	</div>
	<div class="panel-body">
		
		<p>&nbsp;</p>
		
		<div class="row">
			<div class="col-md-36">
			
				<!-- Name -->
				<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('name', Lang::get('domains/messages.create.name').' *', array('class' => 'control-label')) }}
								
							</div>

							<div class="col-md-32">
								{{ Form::text('name', Input::old('name'), array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('name') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Alias -->
				<div class="form-group {{ $errors->has('alias') ? 'has-error' : '' }}">
					<fieldset class="form-inline">
						<div class="row">
							<div class="">
								{{ Form::label('alias', Lang::get('domains/messages.create.alias'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::textarea('alias', Input::old('alias'), array('class'=>'form-control')) }}
								<p class="help-block">{{ $errors->first('alias') }}</p>
							</div>
						</div>
					</fieldset>
				</div>
				
				<!-- Activation Status -->
				<div class="form-group">
					<fieldset class="">
						<div class="row">
							<div class="">
								{{ Form::label('activated', Lang::get('domains/messages.create.activated'), array('class' => 'control-label')) }}
							</div>

							<div class="col-md-32">
								{{ Form::select('activated', array('0'=>Lang::get('general.no'),'1'=>Lang::get('general.yes')), Input::old('activated', 0), array('class'=>'form-control')) }}
							</div>
						</div>
					</fieldset>
				</div>
			
				<p class="help-block">Fields with asterisk (*) are required.</p>
			</div>
			
		</div>

	</div>
</div>

@if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')))
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
									{{ Form::label('users', Lang::get('domains/messages.create.users'), array('class' => 'control-label')) }}
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
	<button type="submit" class="btn btn-primary">Create Domain</button>
</div>

{{ Form::close() }}

@stop