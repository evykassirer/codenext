<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>CodeNext - Sign up</title>
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
                <h2>Sign up!</h2>

<?php

require "login.php";

function ValidUsername($username) {
	if(strlen($username) > 20 || strlen($username) < 4) return false;
	return preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $username);
}

function ValidPassword($password) {
	if(strlen($password) > 20 || strlen($password) < 4) return false;
	return true;
}

function UniqueUsername($username) {
    $STH=$DBH->prepare("SELECT * FROM users WHERE username = :username");
    $STH->setFetchMode(PDO::FETCH_ASSOC);
    $STH->execute(array(":username" => $username));
    $result = $STH->fetchAll();
    if (count($result) > 0)	return false;
}


if ($_POST["username"] && $_POST["password"]) {
    if (!ValidUsername($_POST["username"])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=1" );
        die;
    } else if (!ValidPassword($_POST["password"])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=2" );
        die;
    } else if (!UniqueUsername($_POST["username"])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=3" );
        die;
        
    //we're clear
    } else {
        
        /*$STH=$DBH->prepare("INSERT INTO users VALUES ('', :username, :password_hash)");
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute(array(":username" => $_POST["username"], ":password_hash" => md5($_POST["password"] . $secret)));
        setcookie('CodeNextUsername', $_POST["username"]);
        setcookie('CodeNextPassword', md5($_POST["password"].$key));
        header("Location: index.html" );
        die;*/
        echo "GOOD";
    }
}


   ?>
                
                <form id="login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    
                    <?php

$error = $_GET["error"];
if (isset($error) && $error=="1") {
    echo "<p class='error'>Error: not a valid username.</p>";
} else if (isset($error) && $error=="2") {
    echo "<p class='error'>Error: not a valid password.</p>";
} else if (isset($error) && $error=="3") {
    echo "<p class='error'>Error: username already exists.</p>";
}

                    ?>
                    
                    <div class="line"><label>Username</label> <input type="text" name="username" id="username" /></div>
                    <div class="line"><label>Password</label> <input type="password" name="password" id="password" /></div>
                    <input type="submit" id="submit" value="Login" />
                </form>
            </div>
        </div>
    </body>
</html>