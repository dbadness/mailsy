<!DOCTYPE>

<html>
	<head>
	</head>

	<body>

		<form method='post' action='/smtp-tester'>

			<label for='smtp_server'>Server:</label>
			<input name='smtp_server'>

			<br>

			<label for='username'>Username:</label>
			<input name='username'>

			<br>

			<label for='password'>Password:</label>
			<input name='password'>

			<br>

			<label for='recipient'>Recipient:</label>
			<input name='recipient'>

			<br>

			<label for='subject'>Subject:</label>
			<input name='subject'>

			<br>

			<label for='body'>Body:</label>
			<textarea name='body'></textarea>

			<br>

			<input type='submit' value='send'>

			{!! Form::token() !!}

		</form>

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