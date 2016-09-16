<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Mailsy - Spend your time selling, not emailing</title>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Bootstrap -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="{!! asset('/css/main.css') !!}">
        <script src="{!! asset('/js/main.js') !!}"></script>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        @yield('PageJS')

        <link href="/css/summernote.css" rel="stylesheet">
        <script src="/js/summernote.js"></script>

        <link href="https://bootswatch.com/readable/bootstrap.min.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicons/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/favicons/manifest.json">
        <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-TileImage" content="/favicons/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-71709833-1', 'auto');
            ga('send', 'pageview');
        </script>
    </head>

<body>
    <nav class="navbar navbar-default navbar-fixed-top" style="height: 50px;">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <ul class="nav navbar-nav">
                    <li>
                        <a class="navbar-brand topnav" href="{{ route('home') }}"><img src='/images/logo.png' alt='Mailsy' width='80px'></a>
                    </li>
                </ul>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="{{ route('sendone') }}">Send Email</a>
                    </li>
                    <li>
                        <a href="{{ route('send') }}">Send Email to List</a>
                    </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Templates <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('create') }}">New Template</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('templates') }}">Saved Templates <span class="sr-only">(current)</span></a>
                                    </li>
                                </ul>
                            </li>

                    @if($user && ($user->admin == 'yes'))
                        <li>
                            <a href="{{ route('admin') }}">Team Admin</a>
                        </li>

                    @endif

                </ul>
                <ul class="nav navbar-nav navbar-right">

                    <ul class="nav navbar-nav navbar-right">
                        <ul class="nav navbar-nav">
                            <li><a href="{{ route('settings') }}">{!! $user->email !!}</a></li>
                            <li>
                                @if(!$user->paid)
                                    <a href="{{ route('upgrade') }}">({!! App\User::howManyEmailsLeft() !!} emails left today)</a>
                                @endif
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('home') }}">Dashboard</a>
                                    </li>
                                    <li><a href="{{ route('outbox') }}">Outbox</a>
                                    </li>
                                    <li><a href="{{ route('events') }}">Events</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="{{ route('settings') }}">Settings</a>
                                    </li>
                                    <li><a href="{{ route('tutorial1') }}">Template Tutorial</a>
                                    </li>
                                    <li><a href="{{ route('featuretutorial') }}">Feature Tutorial</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        @if(!$user->paid)
                                            <?php 
                                                // make sure the company has a license to give
                                                $company = App\User::domainCheck($user->email);

                                                if(isset($company->users_left)){
                                                    $teamCheck = true;
                                                } else
                                                {
                                                    $teamCheck = false;
                                                }
                                            ?>
                                            @if($teamCheck)
                                                @if($company->users_left > 0)
                                                    <a href="{{ route('settings') }}">Join Your Team</a>
                                                @endif
                                            @else
                                                <a href="{{ route('upgrade') }}">Upgrade</a>
                                            @endif
                                        @else
                                            <a href="{{ route('settings') }}">Upgraded Account!</a>
                                        @endif
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/logout">Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </ul>
                </div>
            </div>
        </nav>
        <div style="margin:20px 0 0 0;"></div>
        <div class="container">
            <!-- if they're out of emails... -->
            @if($user && (App\User::howManyEmailsLeft() <= 0))
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <br>
                    <br>
                    <br>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> You've reached the maximum number of emails you can send per day on a free account. If you love Mailsy, why not <a class='alert-link' href="{{ route('upgrade') }}">upgrade</a> so you can send unlimited emails per day?
                </div>
            @endif
            <br>
            <br>
            <br>
            @yield('content')
        </div>
        <div style="height:100px;"></div>

    </body>

</html>