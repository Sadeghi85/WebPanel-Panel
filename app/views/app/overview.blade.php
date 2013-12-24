@extends('layouts.default')


@section('title')
	@lang('overview/messages.title') :: @parent
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
<div class="row">
	<div class="col-md-72">
		Total Memory: {{ Libraries\Sadeghi85\Overview::getTotalMemory() }}
		<br>
		Free Memory: {{ Libraries\Sadeghi85\Overview::getFreeMemory() }}
	</div>
</div>
@stop
