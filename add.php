<?php
	require "login.php";
	

	// Get data from form
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

	// TODO: user will be based on their IP address or an id to a database, we make it 0 for now.
	$user = 0;

	// Check if the course is already in the database before updating it.
	$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":url" => $url));
	$course=$STH->fetchAll();
	if (count($course) == 0) {
		$overall = $usefulness + $easiness;
		if ($easiness > 5)
			$overall = $usefulness + 10 - $easiness; 
		$STH=$DBH->prepare("INSERT INTO courses VALUES ('', :name, :prereqs, :subjects, :url, :usefulness, :easiness, :overall)");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $name, ":prereqs" => $prereqs, ":subjects" => $prereqs, ":subjects" => $subjects, ":url" => $url, ":usefulness" => $usefulness, ":easiness" => $easiness, ":overall" => $overall));
	}
	
	// We always add a new review. 
	// TODO: one review per user.
	$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":url" => $url));
	$result = $STH->fetchAll();
	$course_id = $result[0]["id"];
	$STH=$DBH->prepare("INSERT INTO reviews VALUES ('', :user, :course, :usefulness, :easiness, :comments)");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":user" => $user, ":course" => $course_id, ":usefulness" => $usefulness, ":easiness" => $easiness, ":comments" => $comments));

	// If we added a new review to an existing course, update the course data.
	if (count($course) != 0) {
		$STH=$DBH->prepare("SELECT * FROM reviews WHERE course = :id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":id" => $course_id));
		$reviews=$STH->fetchAll();

		$new_usefulness = 0;
		$new_easiness = 0;
		foreach ($reviews as $review) {
			$new_usefulness += $review["usefulness"];
			$new_easiness += $review["easiness"];
		}
		$new_usefulness /= count($reviews);
		$new_easiness /= count($reviews);
		$new_overall = $new_usefulness + $new_easiness;
		if ($new_easiness > 5)
			$new_overall = $new_usefulness + 10 - $new_easiness;
		$STH=$DBH->prepare("UPDATE courses SET usefulness=:usefulness, easiness=:easiness, overall=:overall WHERE id=:id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":usefulness" => $new_usefulness, ":easiness" => $new_easiness, ":overall" => $new_overall, ":id" => $course_id));
	}


	// Update supported subject tags.
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

	// Update prerequisite tags in the same way.
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