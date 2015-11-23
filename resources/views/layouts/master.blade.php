<!DOCTYPE>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="{!! asset('/js/main.js') !!}"></script>
		@yield('pageJS')

		<link rel="stylesheet" href="{!! asset('/css/main.css') !!}">
	</head>
	<body>
		@yield('content')	
	</body>
</html>