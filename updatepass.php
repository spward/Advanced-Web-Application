<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$id = "";

//Keeping track of ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ID'])) {
    $id = $_GET['ID'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ID'])) {
    $id = $_POST['ID'];
} else {
    echo "<p class=\"error\">Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //create variables to store data from form - we never use POST directly w/ user input
    $formdata['password'] = trim($_POST['password']);
    $formdata['newpassword'] = trim($_POST['newpassword']);
    $formdata['confirm'] = trim($_POST['confirm']);



    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/";

    if (preg_match($regex, $formdata['newpassword'])) {
    } else {
        echo "You do not meet the requirements for a password<br>";
        $errormsg = 1;
    }

    if ($formdata['newpassword'] != $formdata['confirm']) {
        $errormsg = 1;
        echo "The passwords do not match.";
    }
    /*  ****************************************************************************
            HASH THE PASSWORD
     **************************************************************************** */
    $securepwd = password_hash($formdata['newpassword'], PASSWORD_BCRYPT);

    if ($errormsg == 1) {
        echo "<p class='error'>There are errors.  Please try again.  Any empty fields have been repopulated with original data.</p>";
    } else {
        $sqlusers = "SELECT * FROM members WHERE username = :username";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':username', $_SESSION['login']);
        $stmtusers->execute();
        $row = $stmtusers->fetch();

        if (password_verify($formdata['password'], $row['password'])) {
            try {
                //query the data
                $sql = "UPDATE members SET password = :password WHERE member_id = :ID ";
                //prepares a statement for execution
                $stmt = $pdo->prepare($sql);
                //binds the actual value of $_GET['ID'] to
                $stmt->bindValue(':password', $securepwd);
                $stmt->bindValue(':ID', $_SESSION['member_id']);
                //executes a prepared statement
                $stmt->execute();
                //hide the form
                $showform = 0;
                //provide useful confirmation to user
                echo "<p>Thanks for updating your password.</p>";
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        } else {

        }
    }//ERRORS
}//POST
if ($showform == 1) {

    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
    $sqlorig = "SELECT * FROM members WHERE member_id = :ID";
    $stmtorig = $pdo->prepare($sqlorig);
    $stmtorig->bindValue(':ID', $id);
    $stmtorig->execute();
    $roworig = $stmtorig->fetch();

    if ($_SESSION['member_id'] != $roworig['member_id']) {
        header("location: profile.php");
    }

    ?>
    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="updatepass.php" novalidate>
        <div class="form-group">
            <label for="password">Old Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>
        </div>

        <div class="form-group">
            <label for="newpassword">New Password</label>
            <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="New Password" required>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>
        </div>

        <div class="form-group">
            <label for="confirm">Confirm Password</label>
            <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm Password" required>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>
        </div>
        <input type="hidden" id="ID" name="ID" value="<?php echo $roworig['member_id']; ?>" />
        <input type="hidden" id="orig" name="orig" value="<?php echo $roworig['username']; ?>" />
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>
    <?php

}//end showform
include_once "footer.php";
?>