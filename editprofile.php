<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$id = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ID'])) {
    $id = $_GET['ID'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['member_id'])) {
    $id = $_POST['member_id'];
} else {
    echo "<p class=\"error\">Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
    $showform = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //create variables to store data from form - we never use POST directly w/ user input
    $formdata['username'] = trim($_POST['username']);
    $formdata['name'] = trim($_POST['name']);
    $formdata['email'] = trim($_POST['email']);


    if ($formdata['username'] != $_POST['username']) {
        /* ****** NEW - CHECK FOR DUPLICATE ENTRIES ****** */
        try {
            $sqlusers = "SELECT * FROM member WHERE username = :username";
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

            if ($_FILES["fileToUpload"]["tmp_name"]) {
                require_once "uploadimage.php";
            }
            //query the data
            $sql = "UPDATE members SET username = :username, name = :name, email = :email, img = :img, twitter = :twitter WHERE member_id = :member_id ";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':name', $formdata['name']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':member_id', $_SESSION['member_id']);
            if ($_FILES["fileToUpload"]["tmp_name"]) {
                $stmt->bindValue(':img', basename($target_file));
            } else {
                $stmt->bindValue(':img', null);
            }
            
            if (isset($formdata['twitter'])) {
                $stmt->bindValue(':twitter', $formdata['twitter']);
            } else {
                $stmt->bindValue(':twitter', null);
            }
            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
            echo "<p>Thanks for updating your information.  See your <a href='profile.php'>updated entry</a>.</p>";
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }//ERRORS
}//POST
if ($showform == 1) {

    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
    $sql = "SELECT * FROM members WHERE member_id = :member_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':member_id', $id);
    $stmt->execute();
    $row = $stmt->fetch();

    ?>
    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="editprofile.php" enctype="multipart/form-data" novalidate>
        
        <?php
            if (is_null($row['img'])) {
                echo "<img class='profile-pic mx-auto my-5 d-block' id='profile-pic' src='images/images.jpg'/>";
            } else {
                echo "
            <img class='profile-pic mx-auto my-5 d-block' id='profile-pic' src='/uploads/" . $row['img'] . "'/>";
            }
        ?>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input name="name" class="form-control" id="name" type="text"
                   value="<?php
                            if (isset($formdata['name']) && !empty($formdata['name'])) {
                                echo $formdata['name'];
                            } else {
                                echo $row['name'];
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
                                echo $row['username'];
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
                                echo $row['email'];
                            }
                            ?>" required />
        </div>

                <div class="form-group">
            <label for="twitter">Twitter Account</label>
            <input name="twitter" class="form-control" id="twitter" type="text"
                   value="<?php
                            if (isset($formdata['twitter']) && !empty($formdata['twitter'])) {
                                echo $formdata['twitter'];
                            } else {
                                echo $row['twitter'];
                            }
                            ?>" />
        </div>

        <div class="form-group">
            <label for="fileToUpload">Profile Picture</label>
            <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload">
        </div>

        <input type="hidden" id="member_id" name="member_id" value="<?php echo $row['member_id']; ?>" />
        <input type="hidden" id="username" name="username" value="<?php echo $row['username']; ?>" />
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>
    <?php

}//end showform
include_once "footer.php";
?>