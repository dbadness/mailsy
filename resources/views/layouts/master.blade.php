<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Mailsy - Individual Emails en Masse</title>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Bootstrap -->
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="{!! asset('/css/main.css') !!}">
		<script src="{!! asset('/js/main.js') !!}"></script>
		@yield('pageJS')

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<nav class="navbar navbar-default">
            <div class="container-fluid" style="width:81%;">
                <div class="navbar-header" style="width:100%;">
                    <a class="navbar-brand" href="/">
                       Mailsy
                    </a>
                    @if($user = Auth::user())
                        <ul class="nav navbar_nav" style="float:left;margin:5px 0 0 0;">
                            <li style="float:left;">
                                <a href="/home">Dashboard</a>
                            </li>
                            <li style="float:left;">
                                <a href="/create">New Email</a>
                            </li>
                            <li style="float:left;">
                                <a href="/settings">Settings</a>
                            </li>
                            <li style="float:left;">
                                <a href="/logout">Log Out</a>
                            </li>
                            <div style="clear:both;"></div>
                        </ul>
                        <p class="navbar-text" style="float:right;">Signed in as {!! $user->email !!}</p>
                    @else
                        <p class="navbar-text" style="float:right;"><a href="/signup">Signup/Login via Gmail</a></p>
                    @endif
                    <div style="clear:both;"></div>
                </div>
            </div>
        </nav>
        <div style="margin:20px 0 0 0;"></div>
        <div class="container">
            @yield('content')
        </div>
        <div style="height:100px;"></div>
        <nav class="navbar navbar-default navbar-fixed-bottom">
            <div class="container"  style="text-align:center;">
                <p class="navbar-text" style="float:none;">©2015 Mailsy Technologies, Inc. Want to say hello or give us 
                feedback of any kind? Send us an email to <a href="mailto:hello@lucolo.com">hello@lucolo.com</a> 
                or reach out to us on <a href="https://www.twitter.com/lucoloinc" target="_blank">Twitter</a>
                or <a href="https://www.facebook.com/lucoloinc" target="_blank">Facebook.</a></p>
            </div>
        </nav>
	</body>
</html>
