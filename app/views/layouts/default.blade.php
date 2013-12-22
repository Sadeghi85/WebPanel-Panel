<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">

  {{-- <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png"> --}}

  <title>
    @section('title')
          {{ Lang::get('app.title') }}
    @show
  </title>

    <!-- Bootstrap -->
    <link href="{{ asset('/assets/css/bootstrap.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/assets/css/bootstrap-theme.css') }}" rel="stylesheet" media="screen">

    <link href="{{ asset('/assets/css/bootstrap-extra.css') }}" rel="stylesheet" media="screen">

 
@section('style') 
   <style type="text/css">
   
   </style>
@show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="{{ asset('/assets/js/html5shiv.js') }}"></script>
      <script src="{{ asset('/assets/js/respond.min.js') }}"></script>
    <![endif]-->
</head>

<body>

@section('navbar')
 <div class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">{{ Lang::get('app.title') }}</a>
    </div>

    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
       {{-- <li class="active"><a href="#">{{ Lang::get('site.accounts') }}</a></li> --}}
        <li><a href="{{ URL::Route('auth.logout') }}">{{ Lang::get('auth/messages.logout.logout') }}</a></li>
      </ul>
    </div><!--/.nav-collapse -->


   </div>
 </div>
@show

@section('container')
<div class="my-fluid-container">
  <div class="row">
    
    <div class="col-md-10">
      <div class="bs-sidebar">
        <ul class="nav bs-sidenav">
          <li class="header">{{ Lang::get('site.tasks') }}</li>
          
          <?php
            $navs = array(
              //array('required' => true, 'label' => Lang::get('site.accounts'), 'routes' => array('default'=>'admin.account.index')),
              //array('required' => true, 'label' => Lang::get('site.create'), 'routes' => array('default'=>'admin.account.create')),
              //array('required' => false, 'label' => Lang::get('site.edit'), 'routes' => array('default'=>'admin.account.index', 'intended' => 'admin.account.edit')),
            );
          ?>
          @foreach ($navs as $nav)

            @if (isset($nav['routes']))
              @if (in_array(Route::currentRouteName(), $nav['routes']))
                <?php $_class = 'active' ?>
              @else
                <?php $_class = '' ?>
              @endif
                @if ($nav['required'] OR $nav['routes']['intended'] == Route::currentRouteName())
                  <li class="{{ $_class }}"><a href="{{ URL::Route($nav['routes']['default']) }}">{{{ $nav['label'] }}}</a></li>
                @endif
            @else
              <li class="divider"></li>
            @endif
          @endforeach
        </ul>
      </div>
   </div><!--/.col-->

   <div class="col-md-62">
     <div class="row">
       <div class="col-md-72">
        @section('content')

        @show
       </div>
     </div>
    </div><!--/.col-->
  </div><!--/.row-->
</div><!--/.container-->
@show

@section('javascript')
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{ asset('/assets/js/jquery-1.10.2.min.js') }}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset('/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap-typeahead.min.js') }}"></script>

@show

</body>
</html>