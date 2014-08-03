
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>CodeNext - Figure out what to learn</title>
    </head>
	<body>
        <div id="main">
            <div id="header">
                <h1>
                    <a href="http://www.pahgawks.com/yc"><img src="logo.png" /></a>
                    <a href="http://www.pahgawks.com/yc">CodeNext</a>
                </h1>
            </div>
            <div class="wrapper">
        <div class='message'><h2>

<?php
	error_reporting(E_ALL);
	require "login.php";
	
	// Formats multi-word tags by changing spaces to underscores, and then
	// puts the tags together in a string separated by underscores. All tags
	// are entered in sentence case (start with capital letter, rest lowercase).
	function FormatTagString($string) {
		$tags = explode(", ", $string);
		foreach ($tags as &$tag) {
			$tag = preg_replace('/\s+/', '_', $tag);
			$tag = ucwords(strtolower($tag));
			$tag = rtrim($tag, ",");
		}
		return implode(",", $tags);
	}

    // Takes two strings of tags and returns a string containing all of the tabs
    // combined (with no duplicates).
	function CombineTags($first, $second) {
		$first = explode(",", $first);
		$second = explode(",", $second);
		foreach ($second as $elem) {
			if (!in_array($elem, $first)) {
				$first[] =  $elem;
			}
		}
		return implode(",", $first);
	}

	// echo "checkpoint";

	// Get data from form
	$name = trim(stripslashes($_POST['name']));
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

	// Get id for this course, if it exists.
	$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":url" => $url));
	$result = $STH->fetchAll();
	// These might be null, but that will work below.
	$course = $result[0];
	$course_id = $course["id"];

	// Update supported subject tags.
	$tags = explode(",", $subjects);
	foreach ($tags as $tag) {
		if($tag == "") 
			continue;
    	$STH=$DBH->prepare("SELECT * FROM tags WHERE name = :name");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $tag));
		$result = $STH->fetchAll();
		// Add the tag if it isn't in the database yet.
		if (count($result) == 0) {
			$STH=$DBH->prepare("INSERT INTO tags VALUES ('', :name, :occurances)");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":name" => $tag, ":occurances" => 1));			
		} else {
			// If it already exists, and isn't listed in the current course, increase the number of occurances.
	    	$STH=$DBH->prepare("SELECT * FROM courses WHERE FIND_IN_SET(:subject, subjects) AND id = :id");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":subject" => $tag, ":id" => $course_id));
			$r = $STH->fetchAll();
			if(count($r) == 0) {
				$tag_id = $result[0]["id"];
				$new_occurances = $result[0]["occurances"] + 1;
				$STH=$DBH->prepare("UPDATE tags SET occurances=:new_occurances WHERE id=:id");
				$STH->setFetchMode(PDO::FETCH_ASSOC);
				$STH->execute(array(":new_occurances" => $new_occurances, ":id" => $tag_id));
			} 
		} 
	}

	// Update prerequisite tags in the same way.
	$tags = explode(",", $prereqs);
	foreach ($tags as $tag) {
		if($tag == "") 
			continue;
    	$STH=$DBH->prepare("SELECT * FROM prereqs WHERE name = :name");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $tag));
		$result = $STH->fetchAll();
		// Add the tag if it isn't in the database yet.
		if (count($result) == 0) {
			$STH=$DBH->prepare("INSERT INTO prereqs VALUES ('', :name, :occurances)");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":name" => $tag, ":occurances" => 1));			
		} else {
			// If it already exists, and isn't listed in the current course, increase the number of occurances.
	    	$STH=$DBH->prepare("SELECT * FROM courses WHERE FIND_IN_SET(:prereq, prereqs) AND id = :id");
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$STH->execute(array(":prereq" => $tag, ":id" => $course_id));
			$r = $STH->fetchAll();
			if(count($r) == 0) {
				$tag_id = $result[0]["id"];
				$new_occurances = $result[0]["occurances"] + 1;
				$STH=$DBH->prepare("UPDATE prereqs SET occurances=:new_occurances WHERE id=:id");
				$STH->setFetchMode(PDO::FETCH_ASSOC);
				$STH->execute(array(":new_occurances" => $new_occurances, ":id" => $tag_id));
			} 
		} 
	}



	// Check if the course is already in the database before updating it.
	$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":url" => $url));
	$new_course = False;
	$check = $STH->fetchAll();
	if (count($check) == 0) {
		$new_course = True;
	}

	if ($new_course) { 
	?>
		This new course has been submitted to our site.

	<?
		$STH=$DBH->prepare("INSERT INTO courses VALUES ('', :name, :prereqs, :subjects, :url, :usefulness, :easiness, :overall)");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":name" => $name, ":prereqs" => $prereqs, ":subjects" => $prereqs, ":subjects" => $subjects, ":url" => $url, ":usefulness" => $usefulness, ":easiness" => $easiness, ":overall" => $overall));
	}

	// We always add a new review. 
	// TODO: one review per user.
	// Get actual id for this course if we just added it.
	if  ($new_course) {
		$STH=$DBH->prepare("SELECT * FROM courses WHERE url = :url");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":url" => $url));
		$result = $STH->fetchAll();
		$course = $result[0];
		$course_id = $course["id"];
	}
	$STH=$DBH->prepare("INSERT INTO reviews VALUES ('', :user, :course, :usefulness, :easiness, :comments)");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":user" => $user, ":course" => $course_id, ":usefulness" => $usefulness, ":easiness" => $easiness, ":comments" => $comments));

	// If we added a new review to an existing course, update the course data.
	if (!$new_course) { 
	?>
		A previously submitted course was updated with your comments.
	<?
		$STH=$DBH->prepare("SELECT * FROM reviews WHERE course = :id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":id" => $course_id) );
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

		$new_prereqs = CombineTags($course["prereqs"], $prereqs);
		$new_subjects = CombineTags($course["subjects"], $subjects);

		$STH=$DBH->prepare("UPDATE courses SET prereqs=:prereqs, subjects=:subjects, usefulness=:usefulness, easiness=:easiness, overall=:overall WHERE id=:id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":prereqs" => $new_prereqs, ":subjects" => $new_subjects, ":usefulness" => $new_usefulness, ":easiness" => $new_easiness, ":overall" => $new_overall, ":id" => $course_id));
	}

?>

					</h2>
					<h3>Thanks!</h3>
				</div>
				<div class="row">
                    <a href="index.html">
                        <span class="type">Return</span>
                    </a>
                </div>                
            </div>
        </div>
    </body>
</html>