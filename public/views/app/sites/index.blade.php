@extends('layouts.default')
<?php if ( ! defined('VIEW_IS_ALLOWED')) { ob_clean(); die(); } ?>

@section('title')
	@lang('sites/messages.index.title') :: @parent
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
		@lang('sites/messages.index.header')

		<div class="pull-right">
			@if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')) or Sentry::getUser()->hasAccess('site.create'))
				<a href="{{ route('sites.create') }}" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> @lang('sites/messages.index.create')</a>
			@else
				<span class="btn btn-sm btn-primary disabled"><i class="glyphicon glyphicon-plus-sign"></i> @lang('sites/messages.index.create')</span>
			@endif
		</div>
		
	</h3>
</div>

{{ $sites->links() }}

<div class="table-responsive">

<table class="table table-hover table-striped table-curved">
	  	<thead>
          <tr>
            <th class="col-md-4">@lang('sites/messages.index.id')</th>
			<th class="col-md-4">@lang('sites/messages.index.tag')</th>
			<th class="col-md-9">@lang('sites/messages.index.server_name')</th>
			<th class="col-md-16">@lang('sites/messages.index.aliases')</th>
			<th class="col-md-4">@lang('sites/messages.index.port')</th>
			<th class="col-md-4">@lang('sites/messages.index.quota')</th>
			<th class="col-md-4">@lang('sites/messages.index.activated')</th>
			<th class="col-md-6">@lang('sites/messages.index.created_at')</th>
			<th class="col-md-6">@lang('sites/messages.index.actions')</th>
          </tr>
      </thead>
	<tbody>
		@if ($sites->count() >= 1)
			@foreach ($sites as $site)
				<tr>
					<td>{{ $site->id }}</td>
					<td>{{ $site->tag }}</td>
					<td>{{ $site->server_name }}</td>
					<td>{{ Site::formatAlias($site->aliases()->lists('alias')) }}</td>
					<td>{{ $site->aliases()->take(1)->pluck('port') }}</td>
					<td>{{ $site->quota }}</td>
					
					<td>@lang('general.' . ($site->isActivated() ? 'yes' : 'no'))</td>
					<td>{{ $site->created_at->diffForHumans() }}</td>
					<td>
						{{ Form::open(array('route' => array('sites.destroy', $site->id), 'method' => 'DELETE', 'id' => 'delete'.$site->id, 'name' => 'Site: '.$site->tag)) }}
							
							@if (Group::isRoot() or Sentry::getUser()->hasAccess('site.edit'))
								<a href="{{ route('sites.edit', $site->id) }}" class="btn btn-xs btn-default">@lang('button.edit')</a>
							@else
								<span class="btn btn-xs btn-default disabled">@lang('button.edit')</span>
							@endif
							
							@if (Group::isRoot() or Sentry::getUser()->hasAccess('site.delete'))
								<button type="button" class="btn btn-xs btn-danger">@lang('button.delete')</button>
							@else
								<span class="btn btn-xs btn-danger disabled">@lang('button.delete')</span>
							@endif
							
						{{ Form::close() }}
					</td>
				</tr>
			@endforeach
		@else
		<tr>
			<td colspan="9">No results</td>
		</tr>
		@endif
	</tbody>
</table>
</div>
{{ $sites->links() }}

<!-- Delete Warning Modal -->
@include('partials/delete_warning_modal')

@stop
