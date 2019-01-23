<?php

require_once "header.php";

session_unset();
session_destroy();
exit();

    $errormsg = 0;
    $securepwd = "";
    $showform = 1;
    $email = "";
    $reset = "";

    //Keeping track of ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['email']) && isset($_GET['reset'])) {
    $email = $_GET['email'];
    $reset = $_GET['reset'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['reset'])) {
    $email = $_POST['email'];
    $reset = $_POST['reset'];
} else {
    echo "<p class=\"error\">Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $formdata['password'] = trim($_POST['password']);
        $formdata['confirm'] = trim($_POST['confirm']);



        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/";

        if (preg_match($regex, $formdata['password'])) {
        } else {
            echo "You do not meet the requirements for a password<br>";
            $errormsg = 1;
        }

        if ($formdata['password'] != $formdata['confirm']) {
            $errormsg = 1;
            echo "The passwords do not match.";
        }



        if($errormsg != 1){
            $securepwd = password_hash($formdata['password'], PASSWORD_BCRYPT);
        echo $securepwd . "<br><br>";
        echo $reset . "<br><br>";;
        try {
            //query the data
            $sql = "UPDATE members SET password = :password WHERE email = :email";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':password', $securepwd);
            $stmt->bindValue(':email', $email);
            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        
        }
    }

    if($showform == 1) {
        //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
        $sql = "SELECT * FROM members WHERE password = :password AND email = :email";
        $sqlstmt = $pdo->prepare($sql);
        $sqlstmt->bindValue(':password', $reset);
        $sqlstmt->bindValue(':email', $email);
        $sqlstmt->execute();
        $row = $sqlstmt->fetch();

        if($reset == $row['password']) {
        } else {
            $errormsg = 1;
            echo "Error in the link.<br>";
        }
        echo "<p>Changing the email address of " . $email . "</p>";
            ?>

        <form method="post" action="changepwd.php">

        <div class="form-group col-lg-6">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <div class="invalid-feedback">
                    Please provide a valid password.
            </div>
        </div>

        <div class="form-group col-lg-6">
            <label for="confirm">Confirm Password</label>
            <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm Password" required>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>

        </div>
        <div class="my-3">
            <input type="hidden" id="email" name="email" value="<?php echo $row['email']; ?>" />
            <input type="hidden" id="reset" name="reset" value="<?php echo $row['password']; ?>" />
            <input type="submit" name="submit" id="submit" value="Submit"/>
        </div>
        </form>
        
        <?php 
    }
require_once "footer.php";
?>