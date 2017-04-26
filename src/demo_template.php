<!DOCTYPE html>
<html class="demo_template">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Weather Conditions Demo</title>
		<?php 
			$content = apply_filters('the_content', $post->post_content); 
			wp_head(); 
		?>
	</head>
	<body class="demo_template">
		<div id="mainContainer">
			Holita :)

			<?php 
				echo $content;
			?>

		</div>
	</body>
</html>