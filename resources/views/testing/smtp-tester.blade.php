<!DOCTYPE>

<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mailsy - Spend your time selling, not emailing</title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/css/landing-page.css" rel="stylesheet">
    <link href="/css/whhg.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">


    <!-- Custom Fonts -->
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="https://bootswatch.com/readable/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
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

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand topnav" href="/"><img src='/images/logo.png' alt='Mailsy' width='80px'></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#hiw">How it Works</a>
                    </li>
                    <li>
                        <a href="#why">Why Use It</a>
                    </li>
                    <li>
                        <a href="#pricing">Pricing</a>
                    </li>
                    <li>
                        <a href="{{ route('signup') }}">Sign Up</a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}">Log In</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

	<div class="container">
	<br>
	<br>
	<br>
	<br>
		<div class="jumbotron text-center">
			<h2>SMTP Tester</h2>
			<form method='post' action='/smtp-tester'>
			<div class="form-group">
				<input name='smtp_server' placeholder="server">
			</div>
			<div class="form-group">
				<input name='username' placeholder="username">
			</div>
			<div class="form-group">
				<input name='password' placeholder="password">
			</div>
<!-- 			<div class="form-group">
				<input name='recipient' placeholder="recipient email">
			</div>
 -->			<div class="form-group">
				<input name='_to' placeholder="email to send from">
			</div>
			<div class="form-group">
				<input name='_from' placeholder="email to send to">
			</div>

			<div class="form-group">
				<input class="btn btn-primary" type='submit' value='send'>
			</div>

				{!! Form::token() !!}

			</form>
		</div>
	</div>

		<?php

			if(isset($_GET['message']))
			{
				if($_GET['message'] == 'success')
				{
					echo '<span style="color:green;">Email Sent.</span>';
				}
				elseif($_GET['message'] == 'error')
				{
					echo '<span style="color:red;">SOmething broke.</span>';
				}
			}
		?>

	</body>
</html>