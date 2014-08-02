<!DOCTYPE html>
<html>
<head>
	<title>
		My Bad Jokes
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Bitter:400' rel='stylesheet' type='text/css'>
	<link href='style.css' rel='stylesheet' type='text/css'>
	<script type="text/javascript">
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		  	xmlhttp = new XMLHttpRequest();
		} else {// code for IE6, IE5
		  	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		window.onload = function() {
			window.onkeyup = function () {
				var textareas = document.getElementsByTagName("textarea");
				var empty=false;
				for (var i=0; i<textareas.length; i++) {
					if (textareas[i].value.replace(/\s/g, '')=="") {
						empty=true;
						break;
					}
				}
				if (empty) {
					document.getElementById("addJoke").disabled=true;
				} else {
					document.getElementById("addJoke").disabled=false;
				}
			}
		};
	</script>
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

			require "login.php";

			$posts = true;
			$user_id = 0;
			$joke_result;

			$STH=$DBH->prepare("SELECT * FROM users WHERE address = :address");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
			$user_result=$STH->fetchAll();

			if (count($user_result) != 0) {
				$user_id = $user_result[0]["id"];
				settype($user_id, "integer");

				$STH=$DBH->prepare("SELECT * FROM jokes WHERE user = :user_id");
				$STH->setFetchMode(PDO::FETCH_ASSOC);
				$STH->execute(array(":user_id" => $user_id));
				$joke_result=$STH->fetchAll();

				if (count($joke_result) == 0) {
					$posts=false;
				}
			} else {
				$posts=false;
			}

			if ($posts) {
				echo "<h2>Jokes I've Posted</h2><p>Here, you can update any of the past submissions from your IP address. If you don't see your jokes here, or see jokes that you don't remember submitting, it could be because of the way your ISP distributes IP addresses.</p>";
				for ($i=0; $i<count($joke_result); $i++) {
					echo "<form class='joke' action='update.php' method='post'>";
					echo "<input type='hidden' name='id' value='" . $joke_result[$i]["id"] . "' />";
					echo "<textarea name='question' maxlength='299'>" . htmlentities($joke_result[$i]["q"]) . "</textarea>";
					echo "<textarea name='answer' maxlength='299'>" . htmlentities($joke_result[$i]["a"]) . "</textarea>";
					echo "<input type='submit' value='Update' />";
					echo "</form>";
				}
			} else {
				echo "<h3 id='warning'>You haven't posted any jokes yet!</h3><h4><a href='index.php' class='button'>Go back</a></h4>";
			}

			$DBH=null;
			?>
		</div>

		<h4><a class="button" href="index.php">Random Joke</a></h4>

		<form id="add" action="add.php" method="post">
			<textarea name="question" placeholder="Question" maxlength="299"></textarea>
			<textarea name="answer" placeholder="Answer" maxlength="299" class="last"></textarea>
			<label>Add more!</label><input id="addJoke" type="Submit" value="Post" disabled="disabled" />
		</form>

		<?php require "footer.php" ?>

	</div>
</body>
</html>