@extends('layouts.default')

@section('title')
	@lang('groups/messages.title') :: @parent
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
		@lang('groups/messages.groups')

		<div class="pull-right">
			<a href="{{ route('groups.create') }}" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-plus-sign"></i> @lang('groups/messages.index.create')</a>
		</div>
	</h3>
</div>

{{ $groups->links() }}

<div class="table-responsive">

<table class="table table-hover table-striped table-curved">
	  	<thead>
          <tr>
            <th class="col-md-4">@lang('groups/messages.index.id')</th>
			<th class="col-md-36">@lang('groups/messages.index.name')</th>
			<th class="col-md-9">@lang('groups/messages.index.users')</th>
			<th class="col-md-9">@lang('groups/messages.index.created_at')</th>
			<th class="col-md-9">@lang('groups/messages.index.actions')</th>
          </tr>
      </thead>
	<tbody>
		@if ($groups->count() >= 1)
		@foreach ($groups as $group)
		<tr>
			<td>{{ $group->id }}</td>
			<td>{{ $group->name }}</td>
			<td>{{ $group->users()->count() }}</td>
			<td>{{ $group->created_at->diffForHumans() }}</td>
			<td>
				{{ Form::open(array('route' => array('groups.destroy', $group->id), 'method' => 'DELETE')) }}
					<a href="{{ route('groups.edit', $group->id) }}" class="btn btn-xs btn-default">@lang('button.edit')</a>
					@if ($group->name !== 'Root')
						<button type="submit" class="btn btn-xs btn-danger">@lang('button.delete')</button>
					@else
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
{{ $groups->links() }}
@stop
