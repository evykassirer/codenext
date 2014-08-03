<!DOCTYPE html>
<html>
<head>
	<title>
		Random Bad Jokes
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Bitter:400' rel='stylesheet' type='text/css'>
	<link href='style.css' rel='stylesheet' type='text/css'>
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
		<div class="message">
			<?php
			$question=trim(stripslashes($_POST['question']));
			$answer=trim(stripslashes($_POST['answer']));
			$id=trim($_POST['id']);
			settype($id, "integer");

			if (!$question || !$answer || !$id) {
				echo "<h3 id='warning'>Something went wrong with your browser's request. Try again in a bit.</h3><h4><a href='me.php' class='button'>Go back</a></h4>";
			} else {

				require "login.php";

				$ok = true;
				$same = false;
				$user_id = 0;
				$date = date("Ymd");

				$STH=$DBH->prepare("SELECT * FROM users WHERE address = :address");
				$STH->setFetchMode(PDO::FETCH_ASSOC);
				$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
				$user_result=$STH->fetchAll();

				if (count($user_result) != 0) {
					$user_id = $user_result[0]["id"];
					settype($user_id, "integer");

					$STH=$DBH->prepare("SELECT * FROM jokes WHERE id=:id");
					$STH->setFetchMode(PDO::FETCH_ASSOC);
					$STH->execute(array(":id" => $id));
					$joke_user=$STH->fetchAll();

					if (count($joke_user)==0 || intval($joke_user[0]["user"]) != intval($user_id)) $ok = false;
					//echo "<div style='display:none'>" . stripslashes($question) . " - " . stripslashes(mysql_result($joke_user,0,"q")) . " - " . mysql_real_escape_string($answer) . " - " . mysql_result($joke_user,0,"a") . "</div>";
					if ($question==$joke_user[0]["q"] && $answer==$joke_user[0]["a"]) $same = true;
				} else {
					$ok = false;
				}

				if ($same) {
					echo "<h3 id='warning'>The joke you're updating is the same as before.</h3><h4><a href='me.php' class='button'>Go back</a></h4>";
				} else if ($ok) {
					$STH=$DBH->prepare("UPDATE jokes SET q=:q, a=:a WHERE id=:id");
					$STH->setFetchMode(PDO::FETCH_ASSOC);
					$STH->execute(array(":q" => $question, ":a" => $answer, ":id" => $id));

					echo "<h3>Joke updated.</h3><h4><a href='index.php?joke=" . $id . "' class='button'>View Joke</a></h4><h4><a href='me.php' class='button'>Go back</a></h4>";
				} else {
					echo "<h3 id='warning'>Something went wrong with your browser's request. Try again in a bit.</h3><h4><a href='me.php' class='button'>Go back</a></h4>";
				}

				$DBH=null;
			}
			?>
			
		</div>

		<?php require "footer.php" ?>

	</div>
</body>
</html>