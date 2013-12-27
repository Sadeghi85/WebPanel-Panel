@extends('layouts.default')

@section('title')
	@lang('users/messages.title') :: @parent
@stop

@section('style')
@parent
	<style type="text/css">

	</style>
@stop

@section('javascript')
@parent
	<script type="text/javascript">
		$( document ).ready(function() {
			
			$(document).on('click', '.btn-danger', function ( event ) {

				if( ! window.confirm("Are you sure?"))
				{
					event.preventDefault();
				}

			});
		});
	</script>
@stop

@section('content')
<div class="page-header">
	<h3>
		@lang('users/messages.users')

		<div class="pull-right">
			<a href="{{ route('users.create') }}" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-plus-sign"></i> @lang('users/messages.index.create')</a>
		</div>
	</h3>
</div>

{{ $users->links() }}

<div class="table-responsive">

<table class="table table-hover table-striped table-curved">
	  	<thead>
          <tr>
            <th class="col-md-4">@lang('users/messages.index.id')</th>
			<th class="col-md-9">@lang('users/messages.index.first_name')</th>
			<th class="col-md-9">@lang('users/messages.index.last_name')</th>
			<th class="col-md-12">@lang('users/messages.index.username')</th>
			<th class="col-md-9">@lang('users/messages.index.activated')</th>
			<th class="col-md-9">@lang('users/messages.index.created_at')</th>
			<th class="col-md-9">@lang('users/messages.index.actions')</th>
          </tr>
      </thead>
	<tbody>
		@if ($users->count() >= 1)
		@foreach ($users as $user)
		<tr>
			<td>{{ $user->id }}</td>
			<td>{{ $user->first_name }}</td>
			<td>{{ $user->last_name }}</td>
			<td>{{ $user->username }}</td>
			<td>@lang('general.' . ($user->isActivated() ? 'yes' : 'no'))</td>
			<td>{{ $user->created_at->diffForHumans() }}</td>
			<td>
				{{ Form::open(array('route' => array('users.destroy', $user->id), 'method' => 'DELETE')) }}
					
					@if (Sentry::getId() !== $user->id and $user->username !== 'root')
						<a href="{{ route('users.edit', $user->id) }}" class="btn btn-xs btn-default">@lang('button.edit')</a>
						<button type="submit" class="btn btn-xs btn-danger">@lang('button.delete')</button>
					@else
						<span class="btn btn-xs btn-default disabled">@lang('button.edit')</span>
						<span class="btn btn-xs btn-danger disabled">@lang('button.delete')</span>
					@endif
				{{ Form::close() }}
			</td>
		</tr>
		@endforeach
		@else
		<tr>
			<td colspan="5">No results</td>
		</tr>
		@endif
	</tbody>
</table>
</div>
{{ $users->links() }}
@stop
