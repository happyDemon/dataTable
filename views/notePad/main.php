<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset=utf-8>
	<title>notePad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <?php echo $assets; ?>
</head>
<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="{{base_url}}">notePad</a>
			</div>
		</div>
	</div>

	<div class="container">
		<?php echo $content; ?>
	
		<hr>

		<footer>
			<p>&copy; Modular Gaming 2013</p>
		</footer>
	</div>

</body>
</html>
