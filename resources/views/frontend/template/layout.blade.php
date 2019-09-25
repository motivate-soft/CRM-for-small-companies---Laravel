<!DOCTYPE html>
<!-- saved from url=(0033)https://biodactil.com/#advantages -->
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biodactil</title>
    <meta name="description"
          content="BIODACTIL se ha convertido en una empresa experta a nivel mundial en tecnología biométrica aportando a los negocios la seguridad que se merecen">
    <meta name="keywords" content="">

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('frontend/img/apple-touch-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('frontend/img/apple-touch-icon-60x60.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/favicon-16x16.png') }}" sizes="16x16">
    @include('frontend.template.style')
</head>
<body>

<div class="preloader animated fadeOut" style="display: none;">
    <img src="{{ asset('/frontend/img/loader.gif') }}" alt="Preloader image">
</div>
<header id="intro" style="opacity: 0; height: 520px!important;" class="animated fadeIn">
    <nav class="navbar original" style="opacity: 0;">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- <a class="navbar-brand" href="#"><img src="img/logo.png" data-active-url="img/logo-active.png" alt=""></a> -->
                <a class="navbar-brand p-l-50" href="{{ backpack_url() }}"><h1 class="">BIODACTIL</h1></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right main-nav">
                    <!-- <li class="home-menu"><a href="#intro" title="Inicio"><i class="fa fa-home" aria-hidden="true"></i></a></li> -->
                    <li><a href="{{ route('plans') }}">{{ trans('fields.plans') }}</a></li>
                    <li><a href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a></li>
                    <li><a href="{{ route('backpack.auth.register') }}">{{ trans('backpack::base.register') }}</a></li>
                    <a href="#" class="close-link"><i class="arrow_up"></i></a>

                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <nav class="navbar navbar-fixed-top" style="position: fixed; top: 0px; margin-top: 0px; opacity: 1;">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- <a class="navbar-brand" href="#"><img src="img/logo.png" data-active-url="img/logo-active.png" alt=""></a> -->
                <a class="navbar-brand p-l-50" href="#intro"><h1 class="">BIODACTIL</h1></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right main-nav">
                    <li><a href="{{ route('plans') }}" class="btn btn-green">{{ trans('fields.plans') }}</a></li>
                    <li><a href="{{ route('backpack.auth.login') }}" class="btn btn-green">{{ trans('backpack::base.login') }}</a></li>
                    <li><a href="{{ route('backpack.auth.register') }}" class="btn btn-green">{{ trans('backpack::base.register') }}</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <div class="container">
        <div class="table">
            <div class="header-text">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <img class="img-responsive center-block" src="{{ asset('frontend/img/logo.png') }}" alt="Biodactil logo">
                        <!-- <h1 class="">BIODACTIL</h1> -->
                        {{--<h2 class="white typed">Tu huella es tu pasaporte</h2>--}}
                        {{--<span class="typed-cursor">|</span>--}}
                    </div>
                    <div class="col-md-12 text-center">
                        {{--<a href="#advantages" class="btn btn-main btn-lg m-t-15"><img--}}
                        {{--src="{{ asset('frontend/img/fingerprint-white.png') }}" alt="fingerprint-white">ENTRAR</a>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
@yield('content')

<footer>
    <div class="container">

        <div class="row bottom-footer text-center-mobile">
            <div class="col-sm-12 text-center">
                <p>© Biodactil 2019. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</footer>
<!-- Holder for mobile navigation -->
<div class="mobile-nav">
    <ul>
        <!-- <li class="home-menu"><a href="#intro" title="Inicio"><i class="fa fa-home" aria-hidden="true"></i></a></li> -->

        <!-- <li><a href="#" data-toggle="modal" data-target="#modal1" class="btn btn-green">Sign Up</a></li> -->
    </ul>
    <a href="#" class="close-link"><i class="arrow_up"></i></a>
</div>

    @include('frontend.template.script')
</body>
</html>
