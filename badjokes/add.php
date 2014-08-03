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

			if (!$question || !$answer) {
				echo "<h3 id='warning'>Something went wrong with your browser's request. Try again in a bit.</h3><h4><a href='index.php' class='button'>Go back</a></h4>";
			} else {

				if (strpos($question, "http")!==false || strpos($answer, "http")!==false) {

					echo "<h3 id='warning'>You cannot submit jokes containing links.</h3>";
					echo "<h4><a href='index.php' class='button'>Go back</a></h4>";

				} else {

					require "login.php";

					$ok = true;
					$user_id = 0;
					$date = date("Ymd");


					$STH=$DBH->prepare("SELECT * FROM users WHERE address = :address");
					$STH->setFetchMode(PDO::FETCH_ASSOC);
					$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
					$user_result=$STH->fetchAll();

					if (count($user_result) != 0) {
						$user_id = $user_result[0]["id"];
						settype($user_id, "integer");

						$STH=$DBH->prepare("SELECT 'id' FROM jokes WHERE user = :user AND date = :date");
						$STH->setFetchMode(PDO::FETCH_ASSOC);
						$STH->execute(array(":user" => $user_id, ":date" => $date));
						$date_result = $STH->fetchAll();

						if (count($date_result) >= 10 && $user_id != 1 && $user_id != 8) {
							$ok = false;
							echo "<h2>You have submitted 10 jokes today. Please wait until tomorrow to submit another.</h2><h4><a href='index.php' class='button'>Go back</a></h4>";
						}
					} else {
						$STH=$DBH->prepare("INSERT INTO users VALUES ('', :address)");
						$STH->setFetchMode(PDO::FETCH_ASSOC);
						$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));

						$STH=$DBH->prepare("SELECT * FROM users WHERE address=:address");
						$STH->setFetchMode(PDO::FETCH_ASSOC);
						$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
						$user_result=$STH->fetchAll();
						$user_id = $user_result[0]["id"];
					}

					if ($ok) {
						$STH=$DBH->prepare("SELECT * FROM jokes WHERE q = :q AND a = :a");
						$STH->setFetchMode(PDO::FETCH_ASSOC);
						$STH->execute(array(":q" => $question, ":a" => $answer));
						$identical_jokes=$STH->fetchAll();

						if (count($identical_jokes)==0) {
							$STH=$DBH->prepare("INSERT INTO jokes VALUES ('', :q, :a, :user_id, :date)");
							$STH->setFetchMode(PDO::FETCH_ASSOC);
							$STH->execute(array(":q" => $question, ":a" => $answer, ":user_id" => $user_id, ":date" => $date));

							$STH=$DBH->prepare("SELECT * FROM jokes WHERE q = :q AND a = :a");
							$STH->setFetchMode(PDO::FETCH_ASSOC);
							$STH->execute(array(":q" => $question, ":a" => $answer));
							$joke_result=$STH->fetchAll();

							$joke_id=$joke_result[0]["id"];
							echo "<h2>Your joke has been added to the collection.</h2><div id='dynamic'><h4><a href='index.php?joke=" . $joke_id . "' class='button'>View Joke</a></h4><h4><a href='index.php' class='button'>Go back</a></h4></div>";
						} else { 
							echo "<h3 id='warning'>There is already an identical joke in the collection.</h3>";
							echo '<form id="add" action="add.php" method="post"><textarea name="question" placeholder="Question" maxlength="299">' . htmlentities($question) .'</textarea><textarea name="answer" placeholder="Answer" maxlength="299" class="last">' . htmlentities($answer) . '</textarea><label>Add your own!</label><input id="addJoke" type="Submit" value="Post" /></form>';
							echo "<h4><a href='index.php' class='button'>Go back</a></h4>"; ?>
							<script type="text/javascript">
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
							</script> <?php
						}
					}
				}

				$DBH = null;
			}
			?>
			
		</div>

		<?php require "footer.php" ?>

	</div>
</body>
</html>