@extends('layouts.default')
<?php if ( ! defined('VIEW_IS_ALLOWED')) { ob_clean(); die(); } ?>

@section('title')
	@lang('domains/messages.index.title') :: @parent
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
		@lang('domains/messages.index.header')

		<div class="pull-right">
			@if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')) or Sentry::getUser()->hasAccess('domain.create'))
				<a href="{{ route('domains.create') }}" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> @lang('domains/messages.index.create')</a>
			@else
				<span class="btn btn-sm btn-primary disabled"><i class="glyphicon glyphicon-plus-sign"></i> @lang('domains/messages.index.create')</span>
			@endif
		</div>
		
	</h3>
</div>

{{ $domains->links() }}

<div class="table-responsive">

<table class="table table-hover table-striped table-curved">
	  	<thead>
          <tr>
            <th class="col-md-4">@lang('domains/messages.index.id')</th>
			<th class="col-md-12">@lang('domains/messages.index.name')</th>
			<th class="col-md-18">@lang('domains/messages.index.alias')</th>
			
			<th class="col-md-9">@lang('domains/messages.index.activated')</th>
			<th class="col-md-9">@lang('domains/messages.index.created_at')</th>
			<th class="col-md-9">@lang('domains/messages.index.actions')</th>
          </tr>
      </thead>
	<tbody>
		@if ($domains->count() >= 1)
			@foreach ($domains as $domain)
				<tr>
					<td>{{ $domain->id }}</td>
					<td>{{ $domain->name }}</td>
					<td>{{ $domain->formatAlias() }}</td>
					
					<td>@lang('general.' . ($domain->isActivated() ? 'yes' : 'no'))</td>
					<td>{{ $domain->created_at->diffForHumans() }}</td>
					<td>
						{{ Form::open(array('route' => array('domains.destroy', $domain->id), 'method' => 'DELETE', 'id' => 'delete'.$domain->id, 'name' => 'Domain: '.$domain->name)) }}
							
							@if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')) or Sentry::getUser()->hasAccess('domain.edit'))
								<a href="{{ route('domains.edit', $domain->id) }}" class="btn btn-xs btn-default">@lang('button.edit')</a>
							@else
								<span class="btn btn-xs btn-default disabled">@lang('button.edit')</span>
							@endif
							
							@if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')) or Sentry::getUser()->hasAccess('domain.delete'))
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
			<td colspan="5">No results</td>
		</tr>
		@endif
	</tbody>
</table>
</div>
{{ $domains->links() }}

<!-- Delete Warning Modal -->
@include('partials/delete_warning_modal')

@stop
