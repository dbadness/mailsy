<!DOCTYPE html>
<html lang="en">

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
                        <a href="{{ route('faq') }}">FAQ</a>
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


    <!-- Header -->
    <a name=""></a>
    <div class="intro-header">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="intro-message">
                        <h1>Send hundreds of personalized emails and get engaged responses.</h1>
                        <h3>Send and track many emails at once with Mailsy - from any email address.</h3>
                        <hr class="intro-divider">
                        <ul class="list-inline intro-social-buttons">
                            <li>
                                <a href="{{ route('signup', [1]) }}" class="btn btn-info btn-lg"><span class="network-name">Signup for Free</span></a>
                                <br>
                                <a href="{{ route('login') }}">Existing User? Login</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.intro-header -->

    <!-- Page Content -->

    <a name="hiw"></a>
    <div class="content-section-a">

        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-sm-6">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">A better mass emailing system means more engaging conversations for you.</h2>
                    <p class="lead">
                        Mailsy allows you to send hundreds of personalized emails directly from your email account in seconds. Recipients are far more likely to reply to a personalized email that came from you - not some thoughtless email marketing service.
                    </p>
                </div>
                <div class="col-lg-5 col-lg-offset-2 col-sm-6">
                    <div style='height:30px;'></div>
                    <iframe src="https://player.vimeo.com/video/150206175" width="100%" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.content-section-a -->
    <a name="why"></a>
    <div class="content-section-b">

        <div class="container">

            <div class="row">
                <div class="col-lg-5 col-lg-offset-1 col-sm-push-6  col-sm-6">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">
                        <ol>
                            <li>Create a template</li>
                            <br>
                            <li>Add recipients</li>
                            <br>
                            <li>Send your emails</li>
                            <br>
                            <li>Engage your audience</li>
                        </ol>
                    </h2>
                </div>
                <div class="col-lg-5 col-sm-pull-6  col-sm-6">
                    <img class="img-responsive" src="/images/recipients.png" alt="">
                </div>
            </div>

    <div class="row text-center" style='margin-top:5%;'>
        <div class="col-md-3">
            <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary">
                </i>
                <i class="fa fa-envelope-square fa-stack-1x fa-inverse">
                </i>
            </span>
            <h4 class="service-heading">
                Email Templates
            </h4>
            <p class="text">
                Using a simple yet powerful templating technology, you can create a template to use over and over again while still being able to define pieces of information for each recipient.
            </p>
        </div>
        <div class="col-md-3">
            <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary">
                </i>
                <i class="fa fa-eye fa-stack-1x fa-inverse">
                </i>
            </span>
            <h4 class="service-heading">
                Email Tracking
            </h4>
            <p class="text">
                Each email sent through Mailsy has a little magic in it so you can get a notification when that recipient opens that email. That way you know when you're on their mind.
            </p>
        </div>
        <div class="col-md-3">
            <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary">
                </i>
                <i class="fa fa-group fa-stack-1x fa-inverse">
                </i>
            </span>
            <h4 class="service-heading">
                CRM Integration
            </h4>
            <p class="text">
                By adding your CRM BCC email into the Settings page, you're configured to let Mailsy send your outreach emails right into your CRM software. Now that's easy.
            </p>
        </div>
        <div class="col-md-3">
            <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary">
                </i>
                <i class="fa fa-upload fa-stack-1x fa-inverse">
                </i>
            </span>
            <h4 class="service-heading">
                Upload CSV Lists
            </h4>
            <p class="text">
                Some people like to add the recipient's information one at a time. Other people like to upload a CSV with the list already made. Who are we to say who's right? You can do both with Mailsy.
            </p>
        </div>
    </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.content-section-b -->

    <a name="pricing"></a>
    <div class="content-section-a">

        <div class="container">

            <div class="row">
                <div class="col-lg-5 col-lg-offset-1 col-sm-push-6  col-sm-6">
                    <table style='width:100%;text-align:center;'>
                        <tr style='color:gray;'>
                            <td> 
                            </td>
                            <td>
                                <h3>Free Account</h3>
                            </td>
                            <td>
                                <h3>Paid Account</h3>
                            </td>
                        </tr>
                        <tr style='border-top:solid 2px lightgray;'>
                            <td style='padding:15px 0 0 0;'>
                                <strong>Monthly Cost per Person</strong>
                            </td>
                            <td>
                                <h3>$0</h3>                            
                            </td>
                            <td> 
                                <h3>$20</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Track Reply Rates when Using Google Account</strong>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Unlimited Templates</strong>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Sent from Your Email Account</strong>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>CSV List Uploads</strong>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Unlimited Support</strong>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Emails per Day</strong>
                            </td>
                            <td>
                                <h3>10</h3>
                            </td>
                            <td>
                                <h3>Unlimited*</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Create and Manage Teams</strong>
                            </td>
                            <td>
                                <h3></h3>
                            </td>
                            <td>
                                <h3><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <a href="{{ route('signup') }}"><button class='btn btn-primary' role='button'>Sign Up for Free</button></a>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <p style='color:lightgray;'>* If you signup with Google, we put a cap of 1,000 emails per day since Google has a email outbound limit on your account and we don't want to lock you out of Gmail!</p>
                </div>
                <div class="col-lg-5 col-sm-pull-6  col-sm-6">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">Free to use with no trial periods.</h2>
                    <p class="lead">
                        You can use your Google or company email credentials to sign up for Mailsy and send 10 emails per day with a free account. There are no trial periods so you can use a free account as long as you'd like.
                        <br><br>
                        If you want to send  unlimited emails per day (or 1,000 per day if you signup with Google), you can upgrade to a paid account for only $20 per month. If you're a team leader, you can set up a team on Mailsy which makes user signup, administration, and billing incredibly simple. 
                        <br><br>
                        As always, you can cancel or go back down to a free account whenever you'd like - there are no yearly contracts or anything like that.
                    </p>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.content-section-a -->
    <a name="contact"></a>
    <div class="banner" style='background:lightgray;'>

        <div class="container">

            <div class="row">
                <div class="col-lg-6">
                    <h2>Reach out with any questions and you'll get a speedy reply.</h2>
                </div>
                <div class="col-lg-6" style='text-align:right;'>
                    <span>hello[at]mailsy[dot]co</span><br>
                    <a href='https://twitter.com/mailsyapp' style='color:white;' target='_blank'><span>@mailsyapp</span></a>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.banner -->

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="list-inline">
                        <li>
                            <a href="#">Home</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="#hiw">How It Works</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="#why">Why Use It</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="#pricing">Pricing</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="mailto:hello@mailsy.co">Contact</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="/faq">FAQ</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="/signup">Sign Up</a>
                        </li>
                        <li class="footer-menu-divider">&sdot;</li>
                        <li>
                            <a href="/login">Log In</a>
                        </li>
                    </ul>
                    <p class="copyright text-muted small">Copyright &copy;<?php echo date('Y');?> Mailsy by Lucolo, Inc. All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Core JavaScript -->
    <script src="/js/bootstrap.min.js"></script>

</body>

</html>
