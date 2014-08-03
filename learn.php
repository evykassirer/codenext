<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>CodeNext - Figure out what to learn</title>
        <script type="text/javascript" src="learn.js"></script>
        <script type="text/javascript">
            window.onload = function() {
                Filter.setInput(document.getElementById("search"), document.getElementById("tags"), true);
                Filter.setInput(document.getElementById("knowledge"), document.getElementById("prereqs"), false);
                var experiences = document.getElementsByClassName("experience");
                for (var i=0; i<experiences.length; i++) {
                    experiences[i].addEventListener("click", function(evt) {
                        if (evt.target.tagName=="DIV") evt.target.parentElement.getElementsByTagName("input")[0].checked=false;
                    });
                }
                
                document.getElementById("next").addEventListener("click", function() {
                    document.getElementById("subject").className="wrapper closed";
                    document.getElementById("experiences").className="wrapper";
                });
                
                document.getElementById("back").addEventListener("click", function() {
                    document.getElementById("subject").className="wrapper";
                    document.getElementById("experiences").className="wrapper closed";
                });
                
                document.getElementById("find").addEventListener("click", function() {
                    var f = document.createElement("form");
                    f.setAttribute('method',"post");
                    f.setAttribute('action',"match.php");
                    
                    var goal = document.createElement("input");
                    goal.name = "goal";
                    goal.type="text";
                    var subjects = document.getElementById("tags").getElementsByClassName("tag");
                    //skip the first one, it's the default
                    for (var i=1; i<subjects.length; i++) {
                        if (subjects[i].getElementsByTagName("input")[0].checked) {
                            goal.value = subjects[i].id;
                            break;
                        }
                    }
                    f.appendChild(goal);
                    
                    var beginner = [];
                    var intermediate = [];
                    var advanced = [];
                    var prereqs = document.getElementsByClassName("prereq");
                    //skip the first one, it's the default
                    for (var i=1; i<prereqs.length; i++) {
                        if (prereqs[i].getElementsByTagName("input")[0].checked) {
                            var radios = prereqs[i].getElementsByClassName("experience")[0].getElementsByTagName("input");
                            if (radios[0].checked) beginner.push(prereqs[i].id);
                            if (radios[1].checked) intermediate.push(prereqs[i].id);
                            if (radios[2].checked) advanced.push(prereqs[i].id);
                        }
                    }
                    
                    for (var i=0; i<beginner.length; i++) {
                        var inp = document.createElement("input");
                        inp.name = "beginner[]";
                        inp.type="text";
                        inp.value = beginner[i];
                        f.appendChild(inp);
                    }
                    for (var i=0; i<intermediate.length; i++) {
                        var inp = document.createElement("input");
                        inp.name = "intermediate[]";
                        inp.type="text";
                        inp.value = intermediate[i];
                        f.appendChild(inp);
                    }
                    for (var i=0; i<advanced.length; i++) {
                        var inp = document.createElement("input");
                        inp.name = "advanced[]";
                        inp.type="text";
                        inp.value = advanced[i];
                        f.appendChild(inp);
                    }
                    
                    var challenge = document.createElement("input");
                    challenge.name = "challenge";
                    challenge.type="text";
                    challenge.value = document.getElementById("easiness").value;
                    f.appendChild(challenge);
                    
                    f.submit();
                });
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
            <div class="wrapper" id="subject">
                <div class="filter">
                    <input type="text" id="search" placeholder="I want to learn..." />
                </div>
                <div class="results" id="tags">
                    <?php

require "login.php";

$STH=$DBH->prepare("SELECT name FROM tags ORDER BY occurances DESC");
$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute();
$result = $STH->fetchAll();
array_unshift($result, array("name" => "Something_new"));
for ($i=0; $i<count($result); $i++) {
    echo "<div class='tag";
    if ($i > 10) echo " closed";
    echo "' id='" . $result[$i]["name"] . "'>";
    echo "<input type='checkbox' name='" . $result[$i]["name"] . "' id='" . $result[$i]["name"] . "box" . "'" . ($i==0?" checked":"") . " />";
    echo "<label for='" . $result[$i]["name"]  . "box" . "' class='title'>" . str_replace("_", " ", $result[$i]["name"]) . "</label>";
    echo "</div>";
}
                    ?>
                </div>
                 <div class="row">
                    <label>I want to learn this in:</label>
                    <select name="length">
                        <option value="hours">hours</option>
                        <option value="days">days</option>
                        <option value="weeks">weeks</option>
                        <option value="months">months</option>
                    </select>
                </div>
                <div class="row slider">
                    <label>Straightforward</label>
                    <input type="range" id="easiness" min="0" max="10" value="5" step="0.5" />
                    <label>Fun Challenge</label>
                </div>
                <div class="row">
                    <a id="next">Next</a>
                </div>
            </div>
            
            
            <!-- experiences -->
            <div class="wrapper closed" id="experiences">
                <div class="filter">
                    <input type="text" id="knowledge" placeholder="I'm familiar with..." />
                </div>
                <div class="results" id="prereqs">
                    <?php

$STH=$DBH->prepare("SELECT name FROM prereqs");
$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute();
$result = $STH->fetchAll();
array_unshift($result, array("name" => "Nothing_yet"));
for ($i=0; $i<count($result); $i++) {
    echo "<div class='tag prereq' id='" . $result[$i]["name"] . "'>";
    
    echo "<input type='checkbox' name='" . $result[$i]["name"] . "' id='" . $result[$i]["name"] . "box2" . "'" . ($i==0?" checked":"") . " />";
    echo "<label for='" . $result[$i]["name"]  . "box2" . "' class='title'>" . str_replace("_", " ", $result[$i]["name"]) . "</label>";
    
    if ($i>0) {
        echo "<div class='experience'>";

        echo "<div class='experienceRow'>";
        echo "<input type='radio' name='experience" . $result[$i]["name"] . "' value='beginner' id='" . $result[$i]["name"]  . "beginner" . "' />";
        echo "<label for='" . $result[$i]["name"]  . "beginner" . "'>Beginner</label>";
        echo "</div>";

        echo "<div class='experienceRow'>";
        echo "<input type='radio' checked name='experience" . $result[$i]["name"] . "' value='intermediate' id='" . $result[$i]["name"]  . "intermediate" . "' />";
        echo "<label for='" . $result[$i]["name"]  . "intermediate" . "'>Intermediate</label>";
        echo "</div>";

        echo "<div class='experienceRow'>";
        echo "<input type='radio' name='experience" . $result[$i]["name"] . "' value='advanced' id='" . $result[$i]["name"]  . "advanced" . "' />";
        echo "<label for='" . $result[$i]["name"]  . "advanced" . "'>Advanced</label>";
        echo "</div>";

        echo "</div>";
    }
    
    echo "</div>";
}

                    ?>
                </div>
                <div class="row">
                    <a id="back">Back</a>
                    <a id="find">Find a course</a>
                </div>
            </div>
        </div>
    </body>
</html>