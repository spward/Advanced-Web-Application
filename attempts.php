<?php

if (isset($_POST["login"]) && isset($_POST["password"])) {
    if (!isset($_SESSION["attempts"])){
        $_SESSION["attempts"] = 1;
    } else {

    if ($_SESSION["attempts"] < 2) {
        echo "Login attempt," . $_SESSION["attempts"] . " try again";
        $_SESSION["attempts"] = $_SESSION["attempts"] + 1;

    } else {
            echo "You've failed too many times, try again in 1 minute.";
            $showform = 0;
            if (!isset($_SESSION['timeout'])) {
                $_SESSION['timeout'] = time();
            }


        }
    }
}

?>


