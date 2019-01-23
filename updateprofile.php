<?php
include_once "header.inc.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$id = "";

//Keeping track of ID
if ($_SERVER["REQUEST_METHOD"] == "SESSION" && isset($_SESSION['member_id'])) {
    $id = $_SESSION['member_id'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ID'])) {
    $id = $_POST['ID'];
} else {
    echo "<p class=\"error\">Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //create variables to store data from form - we never use POST directly w/ user input
    $formdata['username'] = trim($_POST['username']);
    $formdata['name'] = trim($_POST['name']);
    $formdata['email'] = trim($_POST['email']);


    if ($formdata['username'] != $_POST['orig']) {
        /* ****** NEW - CHECK FOR DUPLICATE ENTRIES ****** */
        try {
            $sqlusers = "SELECT * FROM members WHERE username = :username";
            $stmtusers = $pdo->prepare($sqlusers);
            $stmtusers->bindValue(':username', $formdata['username']);
            $stmtusers->execute();
            $countusers = $stmtusers->rowCount();
            if ($countusers > 0) {
                $errormsg = 1;
                echo "<p>The username is already taken.</p>";
            }
        } catch (PDOException $e) {
            echo "<div class='error'><p></p>ERROR selecting users!" . $e->getMessage() . "</p></div>";
            exit();
        }
    }

    if ($errormsg == 1) {
        echo "<p class='error'>There are errors.  Please try again.  Any empty fields have been repopulated with original data.</p>";
    } else {
        try {
            //query the data
            $sql = "UPDATE spwardUser SET username = :username, fname = :fname, email = :email WHERE ID = :ID ";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':fname', $formdata['fname']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':ID', $_SESSION['ID']);
            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
            echo "<p>Thanks for updating your information.  See your <a href='viewuser.php?ID={$_POST['ID']}'>updated entry</a>.</p>";
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }//ERRORS
}//POST
if ($showform == 1) {

    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
    $sqlorig = "SELECT * FROM spwardUser WHERE ID = :ID";
    $stmtorig = $pdo->prepare($sqlorig);
    $stmtorig->bindValue(':ID', $id);
    $stmtorig->execute();
    $roworig = $stmtorig->fetch();

    ?>
    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="updateuser.php" novalidate>
        <div class="form-group">
            <label for="name">First Name</label>
            <input name="name" class="form-control" id="name" type="text"
                   value="<?php
                            if (isset($formdata['name']) && !empty($formdata['name'])) {
                                echo $formdata['name'];
                            } else {
                                echo $roworig['name'];
                            }
                            ?>" required />
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input name="username" class="form-control" id="username" type="text"
                   value="<?php
                            if (isset($formdata['username']) && !empty($formdata['username'])) {
                                echo $formdata['username'];
                            } else {
                                echo $roworig['username'];
                            }
                            ?>" required />
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input name="email" class="form-control" id="email" type="text"
                   value="<?php
                            if (isset($formdata['email']) && !empty($formdata['email'])) {
                                echo $formdata['email'];
                            } else {
                                echo $roworig['email'];
                            }
                            ?>" required />
        </div>
        <input type="hidden" id="ID" name="ID" value="<?php echo $roworig['ID']; ?>" />
        <input type="hidden" id="orig" name="orig" value="<?php echo $roworig['username']; ?>" />
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>
    <?php

}//end showform
include_once "footer.inc.php";
?>