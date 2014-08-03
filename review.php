
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


	$usefulness = trim(stripslashes($_POST['usefulness']));
	$easiness = trim(stripslashes($_POST['easiness']));
	$overall = $usefulness + $easiness;
	if ($easiness > 5)
		$overall = $usefulness + 10 - $easiness; 

	$comments = trim(stripslashes($_POST['comments']));

	// TODO: user will be based on their IP address or an id to a database, we make it 0 for now.
	$user = 0;

	// Get id for this course, if it exists.
	$course_id = trim(stripslashes($_POST['course']));

	$STH=$DBH->prepare("INSERT INTO reviews VALUES ('', :user, :course, :usefulness, :easiness, :comments)");
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$STH->execute(array(":user" => $user, ":course" => $course_id, ":usefulness" => $usefulness, ":easiness" => $easiness, ":comments" => $comments));

	// If we added a new review to an existing course, update the course data.
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

    $STH=$DBH->prepare("UPDATE courses SET usefulness=:usefulness, easiness=:easiness, overall=:overall WHERE id=:id");
    $STH->setFetchMode(PDO::FETCH_ASSOC);
    $STH->execute(array(":usefulness" => $new_usefulness, ":easiness" => $new_easiness, ":overall" => $new_overall, ":id" => $course_id));

?> Your review has been added.

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