<!DOCTYPE html>
<html>
<head>
	<title>
		Random Bad Jokes
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Bitter:400' rel='stylesheet' type='text/css'>
	<link href='style.css' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="jokes.js"></script>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-8777691-1']);
	  _gaq.push(['_setDomainName', 'pahgawks.com']);
	  _gaq.push(['_setAllowLinker', true]);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>
<body style="background:#444">
	<div id="content">
		<div id="dynamic">
			<?php 

			$joke = $_GET['joke'];
			require "getjoke.php";
			if ($joke) {
				unset($joke);
			}

			?>
		</div>

		<form id="add" action="add.php" method="post">
			<textarea name="question" placeholder="Question" maxlength="299"></textarea>
			<textarea name="answer" placeholder="Answer" maxlength="299" class="last"></textarea>
			<label>Add your own!</label><input id="addJoke" type="Submit" value="Post" disabled="disabled" />
		</form>

		<?php require "footer.php" ?>

	</div>
</body>
</html>