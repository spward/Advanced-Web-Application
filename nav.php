<?php

$currentfile = basename($_SERVER['PHP_SELF']);
?>
<div class="container">
    <ul>
        <?php
            echo ($currentfile == "index.php") ? "<li class='active'>Home</li>" : "<li><a href='index.php'>Home</a></li>";
            echo ($currentfile == "contact.php") ? "<li class='active'>Contact Us</li>" : "<li><a href='contact.php'>Contact Us</a></li>";
            echo ($currentfile == "blog.php") ? "<li class='active'>Blog</li>" : "<li><a href='blog.php'>Blog</a></li>";

            if(isset($_SESSION['login'])) {
            echo ($currentfile == "categories.php") ? "<li class='active'>Categories</li>" : "<li><a href='categories.php'>Categories</a></li>";
            }
        ?>
        <div class="user-hub">
            <?php
                if (isset($_SESSION['login'])) {

                    echo ($currentfile == "profile.php") ? "<li class='active'>Profile</li>" : "<li><a href='profile.php'>Profile</a></li>";
                    echo "<li class='pipe'> | </li>";
                    echo ($currentfile == "logout.php") ? "<li class='active'>Logout</li>" : "<li><a href='logout.php'>Logout</a></li>";                
                
                } else {
                    echo ($currentfile == "login.php") ? "<li class='active'>Login</li>" : "<li><a href='login.php'>Login</a></li>";
                    echo "<li class='pipe'> | </li>";
                    echo ($currentfile == "register.php") ? "<li class='active'>Register</li>" : "<li><a href='register.php'>Register</a></li>";

                }
            ?>
        </div>
    </ul>
</div>
