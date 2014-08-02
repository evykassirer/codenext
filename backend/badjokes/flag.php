<?php
$joke_id=$_GET['joke_id'];
settype($joke_id, "integer");

try {

	require "login.php";

	$ok = true;
	$user_id = 0;

	$STH=$DBH->prepare("SELECT * FROM users WHERE address = :address");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
	$user_result=$STH->fetchAll();

	if (count($user_result) != 0) {
		$user_id = $user_result[0]["id"];

		$STH=$DBH->prepare("SELECT id FROM flags WHERE user=:user_id AND joke=:joke_id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":user_id" => $user_id, ":joke_id" => $joke_id));
		$flag_result=$STH->fetchAll();

		if (count($flag_result) != 0 && $user_id != 1) {
			$ok = false;
			echo "You have already flagged this joke.";
		}
	} else {
		$STH=$DBH->prepare("INSERT INTO users VALUES ('', :address)");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));

		$STH=$DBH->prepare("SELECT * FROM users WHERE address=:address");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":address" => $_SERVER["REMOTE_ADDR"]));
		$user_result = $STH->fetchAll();

		$user_id = $user_result[0]["id"];
	}

	if ($ok) {
		settype($user_id, "integer");

		$STH=$DBH->prepare("SELECT id FROM flags WHERE joke=:joke_id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":joke_id" => $joke_id));
		$flags = $STH->fetchAll();

		if (count($flags)>=2 || $user_id == 1) {
			$STH=$DBH->prepare("DELETE FROM jokes WHERE id=:joke_id");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":joke_id" => $joke_id));

			$STH=$DBH->prepare("DELETE FROM flags WHERE joke=:joke_id");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":joke_id" => $joke_id));

			if ($user_id == 1) {
				echo "Joke deleted.";
			} else {
				echo "This joke has been flagged.";
			}

		} else {

			$STH=$DBH->prepare("INSERT INTO flags VALUES ('', :joke_id, :user_id)");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":joke_id" => $joke_id, ":user_id" => $user_id));

			echo "This joke has been flagged.";
			
		}
	}

	$DBH = null;
} catch(PDOException $e) {  
    echo $e->getMessage();  
}


?>