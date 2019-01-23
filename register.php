<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$securepwd = "";
$uploadOk = 1;
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    //create variables to store data from form - we never use POST directly w/ user input
    /* ****** NEW - CHANGED USERNAME TO LOWERCASE ****** */
    $formdata['name'] = trim(strtolower($_POST['name']));
    $formdata['birthday'] = trim(strtolower($_POST['birthday']));
    $formdata['username'] = trim(strtolower($_POST['username']));
    $formdata['email'] = trim(strtolower($_POST['email']));
    $formdata['question_id'] = trim(strtolower($_POST['question_id']));
    $formdata['question_answer'] = trim(strtolower($_POST['question_answer']));
    $formdata['password'] = trim($_POST['password']);
    $formdata['confirm'] = trim($_POST['confirm']);

    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/";
    
    if (preg_match($regex, $formdata['password'])) {
    } else {
        echo "You do not meet the requirements for a password<br>";
        $errormsg = 1;
    }

    if($formdata['password'] != $formdata['confirm']) {
        $errormsg =1;
        echo "The passwords do not match.<br>";
    }

    // Check if there is duplicate users
    try
    {
        $sqlusers = "SELECT * FROM members WHERE username = :username";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':username', $formdata['username']);
        $stmtusers->execute();
        $countusers = $stmtusers->rowCount();
        if ($countusers > 0)
        {
            $errormsg = 1;
            echo "<p>The username is already taken.</p><br>";
        }
    }
    catch (PDOException $e)
    {
        echo "<div class='error'><p></p>ERROR selecting users! " .$e->getMessage() . "</p></div>";
        exit();
    }

        // Check if there is duplicate Emails
    try {
        $sqlemails = "SELECT * FROM members WHERE email = :email";
        $stmtemails = $pdo->prepare($sqlemails);
        $stmtemails->bindValue(':email', $formdata['email']);
        $stmtemails->execute();
        $countemails = $stmtemails->rowCount();
        if ($countemails > 0) {
            $errormsg = 1;
            echo "<p>The Email Address is already taken.</p><br>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'><p></p>ERROR selecting emails! " . $e->getMessage() . "</p></div>";
        exit();
    }



    // Hash the Password
    $securepwd = password_hash($formdata['password'], PASSWORD_BCRYPT);

    if($errormsg == 1)
    {
        echo "<p class='error'>There are errors.  Please make corrections and resubmit.</p>";
    }
    else{
        try{
            if($_FILES["fileToUpload"]["tmp_name"]){
                require_once "uploadimage.php";
            }

            if($uploadOk != 0){
            //query the data
            $sql = "INSERT INTO members (name, birthday, username, email, question_id, question_answer,  password, img, twitter) 
                      VALUES (:name, :birthday, :username, :email, :question_id, :question_answer, :password, :img, :twitter)";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':name', $formdata['name']);
            $stmt->bindValue(':birthday', $formdata['birthday']);
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':question_id', $formdata['question_id']);
            $stmt->bindValue(':question_answer', $formdata['question_answer']);
            $stmt->bindValue(':password', $securepwd);
                if ($_FILES["fileToUpload"]["tmp_name"]) {
                    $stmt->bindValue(':img', basename($target_file));
                } else {
                    $stmt->bindValue(':img', NULL);
                }

                if (isset($formdata['twitter'])) {
                    $stmt->bindValue(':twitter', $formdata['twitter']);
                } else {
                    $stmt->bindValue(':twitter', null);
                }

            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform =0;
            //provide useful confirmation to user
            // header("Location: login.php");
        }
        }
        catch (PDOException $e)
        {
            die( $e->getMessage() );
        }
    } // else errormsg
}//submit
if($showform == 1){
    ?>
    <form class="col-xl-5 col-lg-7 col-md-9 mx-auto mt-5" id="form" name="form" method="POST" action="register.php" enctype="multipart/form-data" novalidate>

        <img class='profile-pic mx-auto my-5 d-block' id='profile-pic' src='images/images.jpg'/>


        <div class="row ">
            <div class="form-group col-lg-6">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" 
                value="<?php if (isset($formdata['name'])) { echo $formdata['name'];} ?>" required>
                <div class="invalid-feedback">
                    Please provide a valid name.
                </div>
            </div>

            <div class="form-group col-lg-6">
                <label for="birthday">Date of Birth</label>
                <input class="form-control" type="date" id="birthday" name="birthday" 
                value="<?php if (isset($formdata['birthday'])) {echo $formdata['birthday'];} ?>" required>
                
                <div class="invalid-feedback">
                 <p>Please provide a valid birthday.</p>
                </div>
            </div>  
        </div>
        

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" 
            value="<?php if (isset($formdata['username'])) {echo $formdata['username'];} ?>" required>

            <div class="invalid-feedback">
                <p>Please provide a valid username.</p>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" 
            value="<?php if (isset($formdata['email'])) {echo $formdata['email']; } ?>" required>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
        </div>

        <div class="form-group">
            <label for="twitter">Twitter Account</label>
            <input type="text" class="form-control" id="twitter" name="twitter" placeholder="Twitter" 
            value="<?php if (isset($formdata['twitter'])) {
                        echo $formdata['twitter'];
                    } ?>">
        </div>

        <div class="form-group">
            <label for="fileToUpload">Profile Picture</label>
            <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload">
        </div>

        <div class="row">

            <div class="form-group col-lg-6">
                <label for="question_id">Security Question</label>
                <select class="form-control" id="question_id" name="question_id">
                    <?php
                        $sql = "SELECT * FROM security_question";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                        echo "<option value='" . $row['question_id'] . "'>" . $row['question'] . "</option>";
                    }

                    ?>
                </select>
            </div>

            <div class="form-group col-lg-6">
                <label for="question_answer">Security Answer</label>
                <input type="text" class="form-control" id="question_answer" name="question_answer" placeholder="Answer" 
                value="<?php if (isset($formdata['question_answer'])) {echo $formdata['question_answer'];} ?>" required>
                
                <div class="invalid-feedback">
                    Please provide an answer to the question.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-lg-6">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <div class="invalid-feedback">
                    Please provide a valid password.
                </div>
                <small id="passHelp" class="form-text text-muted">This will be encrypted for security.</small>
            </div>

            <div class="form-group col-lg-6">
                <label for="confirm">Confirm Password</label>
                <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm Password" required>
                <div class="invalid-feedback">
                    Please provide a valid password.
                </div>
            </div>

        </div>
        <input class="btn btn-primary btn-block mt-3" type="submit" name="submit" id="submit" value="submit"/>
    </form>

    <?php
}//end showform
include_once "footer.php";
?>