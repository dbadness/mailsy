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
        <link href="/css/summernote.css" rel="stylesheet">
        <script src="/js/summernote.js"></script>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

        @yield('PageJS')

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
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-71709833-1', 'auto');
          ga('send', 'pageview');

        </script>
	</head>
	<body>
		<nav class="navbar navbar-default">
            <div class="container-fluid" style="width:81%;">
                <div class="navbar-header" style="width:100%;">
                    <div class="navbar-header">
                        <a class="navbar-brand topnav" href="/"><img src='/images/logo.png' alt='Mailsy' width='80px'></a>
                    </div>
                    @if($user = Auth::user())
                        <ul class="nav navbar_nav" style="float:left;margin:5px 0 0 0;">
                            <li style="float:left;">
                                <a href="/home">Dashboard</a>
                            </li>
                            <li style="float:left;">
                                <a href="/create">New Template</a>
                            </li>
                            <li style="float:left;">
                                <a href="/settings">Settings</a>
                            </li>
                            <li style="float:left;">
                                <a href="/logout">Log Out</a>
                            </li>
                            <li style="float:left;background:#00EE76;border-radius:5px;">
                                <a href="/tutorial/step1" style='color:#2E8B57;'>Tutorial</a>
                            </li>
                            <div style="clear:both;"></div>
                        </ul>
                        <p class="navbar-text" style="float:right;">
                            Signed in as {!! $user->email !!} 
                            @if(!$user->paid)
                                ({!! App\User::howManyEmailsLeft() !!} emails left today)
                                @if($user->status == 'paying')
                                    <a href='/membership/add'>Upgrade Myself</a>
                                @else
                                    <a href='/upgrade'>Upgrade</a>
                                @endif
                            @else
                                (Upgraded Account!)
                            @endif
                        </p>
                    @else
                        <p class="navbar-text" style="float:right;"><a href="/signup">Signup/Login via Gmail</a></p>
                    @endif
                    <div style="clear:both;"></div>
                </div>
            </div>
        </nav>
        <div style="margin:20px 0 0 0;"></div>
        <div class="container">
            <!-- if they're out of emails... -->
            @if($user && (App\User::howManyEmailsLeft() == 0))
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> You've reached the maximum number of emails you can send per day on a free account. If you love Mailsy, why not <a class='alert-link' href='/upgrade'>upgrade</a> so you can send unlimited emails per day?
                </div>
            @endif
            @yield('content')
        </div>
        <div style="height:100px;"></div>
        <nav class="navbar navbar-default navbar-fixed-bottom">
            <div class="container"  style="text-align:center;">
                <p class="navbar-text" style="float:none;">Copyright &copy;<?php echo date('Y');?> Mailsy by Lucolo, Inc. Questions? Feedback? Movie recommendations? Send an email to <a href="mailto:hello@mailsy.co">hello@mailsy.co</a> 
                or reach out on <a href="https://www.twitter.com/mailsyapp" target="_blank">Twitter</a>.</p>
            </div>
        </nav>
	</body>
</html>
