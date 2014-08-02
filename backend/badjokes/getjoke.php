<?php 
$last=$_GET['last'];
settype($last, "integer");
if (!$joke && $_GET['joke']) $joke = $_GET['joke'];
settype($joke, "integer");

try { 
	require "login.php";

	$result;
	$STH;
	if ($joke) {
		$STH=$DBH->prepare("SELECT * FROM jokes WHERE id=:joke");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":joke" => $joke));
		$result = $STH->fetchAll();

		if (count($result) == 0) {
			echo "<div id='warning'>The URL you went to isn't a valid joke, so here's a random one instead.</div>";
			$STH = $DBH->query("SELECT id FROM jokes");
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			$result = $STH->fetchAll();
		}
	} else {
		$STH = $DBH->query("SELECT id FROM jokes");
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		$result = $STH->fetchAll();
	}

	if (count($result) == 0) {
		echo "<h2>No jokes yet.</h2>";
	} else {
		$i = rand(0, count($result)-1);
		if ($last) {
			while ($result[$i]["id"] == intval($last)) $i = rand(0, count($result)-1);
		}
		$STH=$DBH->prepare("SELECT * FROM jokes WHERE id=:id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":id" => $result[$i]["id"]));
		$joke_result=$STH->fetchAll();
		$q=$joke_result[0]["q"];
		$a=$joke_result[0]["a"];
		$id=$joke_result[0]["id"];
		echo "<h2>" . htmlentities($q) . "</h2><h3>" . htmlentities($a) . "</h3><input type='hidden' id='jokeId' value='" . $id . "' />";
		
		echo "<input type='button' value='Random Joke' id='randomButton' /><input type='button' id='flagButton' value='Flag as Inappropriate' />";
	}

	$DBH = null;
} catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>