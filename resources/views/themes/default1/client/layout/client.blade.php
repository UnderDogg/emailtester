<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title> @yield('title') Localhost Corporation | Email tester (guest user) </title>
    <!-- faveo favicon -->
    <link rel="shortcut icon" href="{{asset("lb-faveo/media/images/favicon.ico")}}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> --}}
    <link href="{{asset("lb-faveo/css/bootstrap.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset("lb-faveo/css/AdminLTEsemi.css")}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    {{-- <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" /> --}}
    <link href="{{asset("lb-faveo/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css"/>
    <!-- Ionicons -->
    {{-- <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" /> --}}
    <link href="{{asset("lb-faveo/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
    <!-- fullCalendar 2.2.5-->
    <link href="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}" rel="stylesheet"
          type="text/css"/>
    <!-- Theme style -->
    <link href="{{asset("lb-faveo/css/jquery.rating.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset("lb-faveo/css/app.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}" rel="stylesheet"
          type="text/css"/>

    {{-- // <script src="ckeditor/ckeditor.js"></script> --}}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    {{-- // <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <script src="{{asset("lb-faveo/js/jquery2.1.1.min.js")}}" type="text/javascript"></script>
    @yield('HeadInclude')
</head>
<body>
<div id="page" class="hfeed site">
    <header id="masthead" class="site-header" role="banner">
        <div class="container" style="">
            <div id="logo" class="site-logo text-center" style="font-size: 30px;">
                <a href="http://www.localhost.com" rel="home">
                    Localhost Corporation | email tester
                </a>

            </div><!-- #logo -->
            <div id="navbar" class="navbar-wrapper text-center">
                <nav class="navbar navbar-default site-navigation" role="navigation">
                    <ul class="nav navbar-nav navbar-menu">
                        <li @yield('home')><a href="{{url('/')}}">{!! Lang::get('lang.home') !!}</a></li>
                        @if(Auth::user())
                            <li @yield('myticket')><a
                                        href="{{url('mytickets')}}">{!! Lang::get('lang.my_tickets') !!}</a></li>

                            {{-- <li @yield('contact')><a href="{{route('contact')}}">Contact us</a></li> --}}
                            <li @yield('profile')><a href="#">{!! Lang::get('lang.my_profile') !!}</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="banner-wrapper user-menu text-center clearfix">
                                            @if(Auth::user()->profile_pic)
                                                <img src="{{asset('lb-faveo/media/profilepic')}}{{'/'}}{{Auth::user()->profile_pic}}"
                                                     class="img-circle" alt="User Image" height="80" width="80"/>
                                            @endif
                                            <h3 class="banner-title text-info h4">{{Auth::user()->first_name." ".Auth::user()->last_name}}</h3>

                                            <div class="banner-content">
                                                {{-- <a href="{{url('kb/client-profile')}}" class="btn btn-custom btn-xs">Edit Profile</a> --}}
                                                <a href="{{url('auth/logout')}}"
                                                   class="btn btn-custom btn-xs">{!! Lang::get('lang.log_out') !!}</a>
                                            </div>
                                            @if(Auth::user())
                                                @if(Auth::user()->role != 'user')
                                                    <div class="banner-content">
                                                        <a href="{{url('dashboard')}}"
                                                           class="btn btn-custom btn-xs">{!! Lang::get('lang.dashboard') !!}</a>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(Auth::user())
                                                @if(Auth::user()->role == 'user')
                                                    <div class="banner-content">
                                                        <a href="{{url('client-profile')}}"
                                                           class="btn btn-custom btn-xs">{!! Lang::get('lang.profile') !!}</a>
                                                    </div>
                                                @endif
                                            @endif

                                        </div>
                                    </li>
                                </ul>
                            </li>
                    </ul><!-- .navbar-user -->
                    @else
                        <ul class="nav navbar-nav navbar-login">
                            <li <?php if($errors->first('email') || $errors->first('password')){ ?> class="sfHover" <?php } else { ?> <?php } ?> >
                                <a href="#" data-toggle="collapse"
                                   <?php if($errors->first('email') || $errors->first('password')){
                                   } else { ?> class="collapsed"
                                   <?php  } ?> data-target="#login-form">{!! Lang::get('lang.login') !!} <i
                                            class="sub-indicator fa fa-chevron-circle-down fa-fw text-muted"></i></a>
                            </li>
                        </ul><!-- .navbar-login -->
                        <div id="login-form"
                             <?php if($errors->first('email') || $errors->first('password')){ ?> class="login-form collapse fade clearfix in"
                             <?php } else { ?> class="login-form collapse fade clearfix" <?php  } ?> >
                            {!!  Form::open(['action'=>'Auth\AuthController@postLogin', 'method'=>'post']) !!}

                            <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                                {!! Form::text('email',null,['placeholder'=>Lang::get('lang.e-mail'),'class' => 'form-control']) !!}
                                {!! $errors->first('email', '<spam class="help-block">:message</spam>') !!}
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                                {!! Form::password('password',['placeholder'=>Lang::get('lang.password'),'class' => 'form-control']) !!}
                                {!! $errors->first('password', '<spam class="help-block">:message</spam>') !!}
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <ul class="list-unstyled pull-left">
                                <li>
                                    <a href="{{url('password/email')}}">{!! Lang::get('lang.forgot_password') !!}</a><br>
                                </li>
                                <li><a href="{{url('auth/register')}}">{!! Lang::get('lang.create_account') !!}</a></li>
                            </ul>
                            <button type="submit"
                                    class="btn btn-custom pull-right">{!! Lang::get('lang.login') !!}</button>
                            {!! Form::close() !!}
                        </div><!-- #login-form -->
                    @endif
                </nav><!-- #site-navigation -->
            </div><!-- #navbar -->

            <div id="header-search" class="site-search clearfix" style="padding-bottom:5px"><!-- #header-search -->
                {!!Form::open(['method'=>'get','action'=>'Client\kb\UserController@search','class'=>'search-form clearfix'])!!}
                <div class="form-border">

                    <div class="form-inline ">
                        <div class="form-group">
                            <input type="text" name="s" class="search-field form-control input-lg"
                                   title="Enter search term"
                                   placeholder="{!! Lang::get('lang.have_a_question?_type_your_search_term_here') !!}"/>
                        </div>
                        <button type="submit"
                                class="search-submit btn btn-custom btn-lg pull-right">{!! Lang::get('lang.search') !!}</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <!-- Right side column. Contains the navbar and content of the page -->
    @yield('breadcrumb')
            <!-- Main content -->
    <div id="main" class="site-main clearfix">
        <div class="container">
            <div class="content-area">
                <div class="row">
                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa  fa-check-circle"></i>
                            <b>Success!</b>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{Session::get('success')}}
                        </div>
                        @endif
                                <!-- failure message -->
                        @if(Session::has('fails'))
                            <div class="alert alert-danger alert-dismissable">
                                <i class="fa fa-ban"></i>
                                <b>Alert!</b> Failed.
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                {{Session::get('fails')}}
                            </div>
                        @endif

                        @yield('content')
                        <div id="sidebar" class="site-sidebar col-md-3">
                            <div class="widget-area">
                                <section id="section-banner" class="section">
                                    @yield('check')
                                </section><!-- #section-banner -->
                                <section id="section-categories" class="section">
                                    @yield('category')
                                </section><!-- #section-categories -->
                            </div>
                        </div><!-- #sidebar -->
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="site-info col-md-6">
                    <p class="text-muted">{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!} <a
                                href="http://www.localhost.com">Localhost
                            Corporation</a>. {!! Lang::get('lang.all_rights_reserved') !!}
                        . {!! Lang::get('lang.powered_by') !!} <a href="http://www.localhost.com/"
                                                                  target="_blank">Faveo</a>
                    </p>
                </div>
                <div class="site-social text-right col-md-6">
                    <ul class="list-inline hidden-print">


                        <li><a href="#" class="btn btn-social btn-facebook"><i
                                        class="fa fa-facebook fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-twitter"><i
                                        class="fa fa-twitter fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-google-plus"><i
                                        class="fa fa-google-plus fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-linkedin"><i
                                        class="fa fa-linkedin fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-vimeo"><i
                                        class="fa fa-vimeo-square fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-youtube"><i
                                        class="fa fa-youtube-play fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-flickr"><i
                                        class="fa fa-flickr fa-fw"></i></a></li>


                        <li><a href="#" class="btn btn-social btn-rss"><i
                                        class="fa fa-rss fa-fw"></i></a></li>


                    </ul>
                </div>
            </div>
    </footer><!-- #colophon -->
    <!-- jQuery 2.1.3 -->
    {{-- // <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <script src="{{asset("lb-faveo/js/jquery2.1.1.min.js")}}" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    {{-- // <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script> --}}
    <script src="{{asset("lb-faveo/js/bootstrap.min.js")}}" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="{{asset("lb-faveo/js/superfish.js")}}" type="text/javascript"></script>
    <script src="{{asset("lb-faveo/js/mobilemenu.js")}}" type="text/javascript"></script>
    <script src="{{asset("lb-faveo/js/know.js")}}" type="text/javascript"></script>
    <script src="{{asset("lb-faveo/js/jquery.rating.pack.js")}}" type="text/javascript"></script>
    <script src="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js")}}"
            type="text/javascript"></script>
    <script src="{{asset("lb-faveo/plugins/iCheck/icheck.min.js")}}" type="text/javascript"></script>
    <script>
        $(function () {

            //Enable check and uncheck all functionality
            $(".checkbox-toggle").click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $("input[type='checkbox']", ".mailbox-messages").iCheck("uncheck");
                } else {
                    //Check all checkboxes
                    $("input[type='checkbox']", ".mailbox-messages").iCheck("check");
                }
                $(this).data("clicks", !clicks);
            });

            //Handle starring for glyphicon and font awesome
            $(".mailbox-star").click(function (e) {
                e.preventDefault();
                //detect type
                var $this = $(this).find("a > i");
                var glyph = $this.hasClass("glyphicon");
                var fa = $this.hasClass("fa");

                //Switch states
                if (glyph) {
                    $this.toggleClass("glyphicon-star");
                    $this.toggleClass("glyphicon-star-empty");
                }

                if (fa) {
                    $this.toggleClass("fa-star");
                    $this.toggleClass("fa-star-o");
                }
            });
        });
    </script>
</body>
</html>