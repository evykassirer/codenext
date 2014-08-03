<?php
	require "login.php";
	
	$name = trim(stripslashes($_POST['name']));

	function FormatTagString($string) {
		$tags = explode(", ", $string);
		foreach ($tags as &$tag) {
			$tag = preg_replace('/\s+/', '_', $tag);
		}
		return implode(",", $tags);
	}

	$prereqs = trim(stripslashes($_POST['prereqs']));
	$prereqs = FormatTagString($prereqs);

	$subjects = trim(stripslashes($_POST['subjects']));
	$subjects = FormatTagString($subjects);

	$url = trim(stripslashes($_POST['url']));
	$parse = parse_url($url);
	$url = preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']) . $parse['path'];
	// print_r($url);

	$usefulness = trim(stripslashes($_POST['usefulness']));
	$easiness = trim(stripslashes($_POST['easiness']));
	$overall = $usefulness + $easiness;
	if ($easiness > 5)
		$overall = $usefulness + 10 - $easiness; 

	$comments = trim(stripslashes($_POST['comments']));

	// CHECK FOR URL DUPLICATES LATER
	/*
	$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
					$STH->setFetchMode(PDO::FETCH_ASSOC);
					$STH->execute(array(":address" => $url));
					$user_result=$STH->fetchAll();

					if (count($user_result) != 0) {
	*/

	$STH=$DBH->prepare("INSERT INTO courses VALUES ('', :name, :prereqs, :subjects, :url, :usefulness, :easiness, :overall)");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":name" => $name, ":prereqs" => $prereqs, ":subjects" => $prereqs, ":subjects" => $subjects, ":url" => $url, ":usefulness" => $usefulness, ":easiness" => $easiness, ":overall" => $overall));

	$STH=$DBH->prepare("SELECT id FROM courses WHERE url = :url");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":url" => $url));
	$result = $STH->fetchAll();
	$course_id = $result[0]["id"];

	// user will be based on their IP address
	$user = 0;

	$STH=$DBH->prepare("INSERT INTO reviews VALUES ('', :user, :course, :usefulness, :easiness, :comments)");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":user" => $user, ":course" => $course_id, ":usefulness" => $usefulness, ":easiness" => $easiness, ":comments" => $comments));

	// Add subject tags.
	$tags = explode(",", $subjects);
	foreach ($tags as $tag) {
    	$STH=$DBH->prepare("SELECT 1 FROM tags WHERE name = :name");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $tag));
		$result = $STH->fetchAll();
		// Add the tag if it isn't in the database yet.
		if (count($result) == 0) {
			$STH=$DBH->prepare("INSERT INTO tags VALUES ('', :name)");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":name" => $tag));			
		}
	}

	// Add prerequisite tags in the same way.
	$tags = explode(",", $prereqs);
	foreach ($tags as $tag) {
    	$STH=$DBH->prepare("SELECT 1 FROM prereqs WHERE name = :name");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $tag));
		$result = $STH->fetchAll();
		// Add the tag if it isn't in the database yet.
		if (count($result) == 0) {
			$STH=$DBH->prepare("INSERT INTO prereqs VALUES ('', :name)");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":name" => $tag));			
		}
	}


?>