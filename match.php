<?php
	require "login.php";
	// var_dump($_POST);
	
	// Get data from user
	$beg_skills = array();
	if(isset($_POST['beginner'])) 
		$beg_skills = $_POST['beginner'];
	// print_r($beg_skills);


	$int_skills = array();
	if(isset($_POST['intermediate'])) 
		$int_skills = $_POST['intermediate'];
	// print_r($int_skills);


	$adv_skills = array();
	if(isset($_POST['advanced'])) 
		$int_skills = $_POST['adv'];	
	// print_r($adv_skills);

	$goal_skill = "";
	if(isset($_POST['goal']))
		$goal_skill = trim(stripslashes($_POST['goal']));
	// print_r($goal_skill);

	$user_challenge = trim(stripslashes($_POST['challenge']));
	//print_r($user_challenge);

	if($goal_skill != "") {
		// Find courses that teach the skill the user is looking for.
		// There is gaurenteed ot be a match because we only let them choose skills that we support.
		$STH=$DBH->prepare("SELECT * FROM courses WHERE FIND_IN_SET(:subject, subjects)");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":subject" => $goal_skill));
		$courses = $STH->fetchAll();
	} else {
		$STH=$DBH->prepare("SELECT * FROM courses");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute();
		$courses = $STH->fetchAll();		
	}

	foreach ($courses as &$course) {
		echo "---- COURSE: ";
		echo $course["name"];

		$prereqs = $course["prereqs"];
		$prereqs = explode(",", $prereqs);
		
		// DIFFICULTY ****************
		$difficulty_score = 0;
		foreach ($beg_skills as $skill){
			if (in_array($skill, $prereqs))
				$difficulty_score += 1;
			else
				$difficulty_score += 0.5;
			if ($difficulty_score >= 4) {
				$difficulty_score = 4;
				break;
			}
		}
		foreach ($int_skills as $skill){
			if (in_array($skill, $prereqs))
				$difficulty_score += 2;
			else
				$difficulty_score += 1;
			if ($difficulty_score >= 7) {
				$difficulty_score = 7;
				break;
			}
		}
		foreach ($adv_skills as $skill){
			if (in_array($skill, $prereqs))
				$difficulty_score += 3;
			else
				$difficulty_score += 1.5;
			if ($difficulty_score >= 10) {
				$difficulty_score = 10;
				break;
			}
		}
		$difficulty_score *= 5;
		echo " difficulty score: ";
		echo $difficulty_score;
		echo "/50. ";

		// PREREQUISITE SKILL MATCHING
		$prereq_score = 0;
		foreach ($prereqs as $skill) {
			if (in_array($beg_skills, $skill))
				$prereq_score += 1;
			else if (in_array($int_skills, $skill))
				$prereq_score += 2;
			else if (in_array($adv_skills, $skill))
				$prereq_score += 3;
		}
		if(count($prereqs) > 0)
			$prereq_score = ($prereq_score / (3 * count($prereqs))) * 50;
		else 
			$prereq_score = 50;
		echo " prereq match score: ";
		echo $prereq_score;
		echo "/50. ";

		$final_score = ($difficulty_score + $prereq_score)  * ($course["usefulness"] / 10);
		echo "Final score: ";
		echo $final_score;
		echo "/100. ";

		$course["score"] = $final_score;
	}

	// SORT COURSES BY SCORE\
	function cmp($a, $b) {
    	if ($a["score"] == $b["score"]) {
        	return 0;
	    }
	    return ($a["score"] < $b["score"]) ? 1 : -1;
	}

	usort($courses, "cmp");

	// SELECT RESULT:
	$winner = $courses[0];
	if($winner["score"] == 0)
		echo "Sorry, no courses found that match your goals and skills. Try another combination or come back later and try again!";
	else {
		echo "DA WINNER IS: ";
		echo $winner["name"];
		echo " ";
		echo $winner["url"];
		echo "users' comments: ";
		$STH=$DBH->prepare("SELECT * FROM reviews WHERE course = :id");
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$STH->execute(array(":id" => $winner["id"]));
		$result = $STH->fetchAll();
		foreach ($result as $review) {
			echo $review["comments"];
			echo "---";
		}
	}


?>