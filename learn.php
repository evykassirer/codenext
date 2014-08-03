<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>CodeNext - Figure out what to learn</title>
        <script type="text/javascript" src="learn.js"></script>
    </head>
	<body>
        <div id="learn">
            <div id="header">
                <h1>
                    <a href="http://www.pahgawks.com/yc"><img src="logo.png" /></a>
                    <a href="http://www.pahgawks.com/yc">CodeNext</a>
                </h1>
            </div>
            <div class="wrapper">
                <div class="filter">
                    <input type="text" id="search" placeholder="I want to learn..." />
                </div>
                <div class="results">
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
        </div>
    </body>
</html>