<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>CodeNext - Figure out what to learn</title>
        <script type="text/javascript" src="learn.js"></script>
        <script type="text/javascript">
            window.onload = function() {
                Filter.setInput(document.getElementById("search"), document.getElementById("tags"), true);
            };
        </script>
    </head>
	<body>
        <div id="learn">
            <div id="header">
                <h1>
                    <a href="http://www.pahgawks.com/yc"><img src="logo.png" /></a>
                    <a href="http://www.pahgawks.com/yc">CodeNext</a>
                </h1>
            </div>
            
            <!-- subject -->
            <div class="wrapper">
                <div class="filter">
                    <input type="text" id="search" placeholder="I want to learn..." />
                </div>
                <div class="results" id="tags">
                    <?php

require "login.php";

$STH=$DBH->prepare("SELECT name FROM tags");
$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute();
$result = $STH->fetchAll();

for ($i=0; $i<count($result); $i++) {
    echo "<div class='tag' id='" . $result[$i]["name"] . "'>";
    echo "<input type='checkbox' name='" . $result[$i]["name"] . "' id='" . $result[$i]["name"] . "box" . "' />";
    echo "<label for='" . $result[$i]["name"]  . "box" . "' class='title'>" . $result[$i]["name"] . "</label>";
    echo "</div>";
}

                    ?>
                </div>
            </div>
            
            
            <!-- knowledge -->
            <div class="wrapper">
                <div class="filter">
                    <input type="text" id="knowledge" placeholder="I'm familiar with..." />
                </div>
                <div class="results" id="prereqs">
                    <?php

$STH=$DBH->prepare("SELECT name FROM prereqs");
$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute();
$result = $STH->fetchAll();

for ($i=0; $i<count($result); $i++) {
    echo "<div class='tag prereq' id='" . $result[$i]["name"] . "'>";
    
    echo "<div class='experience'>";
    
    echo "<div class='experienceRow'>";
    echo "<input type='radio' name='experience' value='expert' id='" . $result[$i]["name"]  . "expert" . "' />";
    echo "<label for='" . $result[$i]["name"]  . "expert" . "'>Expert</label>";
    echo "</div>";
    
    echo "<div class='experienceRow'>";
    echo "<input type='radio' name='experience' value='intermediate' id='" . $result[$i]["name"]  . "intermediate" . "' />";
    echo "<label for='" . $result[$i]["name"]  . "expert" . "'>Intermediate</label>";
    echo "</div>";
    
    echo "<div class='experienceRow'>";
    echo "<input type='radio' name='experience' value='beginner' id='" . $result[$i]["name"]  . "beginner" . "' />";
    echo "<label for='" . $result[$i]["name"]  . "beginner" . "'>Beginner</label>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<input type='checkbox' name='" . $result[$i]["name"] . "' id='" . $result[$i]["name"] . "box" . "' />";
    echo "<label for='" . $result[$i]["name"]  . "box" . "' class='title'>" . $result[$i]["name"] . "</label>";
    echo "</div>";
}

                    ?>
                </div>
            </div>
        </div>
    </body>
</html>