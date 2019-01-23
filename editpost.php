<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$id = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
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

            require_once "uploadimage.php";
            //query the data
            $sql = "UPDATE members SET username = :username, name = :name, email = :email, img = :img WHERE member_id = :member_id ";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':name', $formdata['name']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':member_id', $_SESSION['member_id']);
            $stmt->bindValue(':img', basename($target_file));
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
    $sql = "SELECT * FROM posts WHERE post_id = :post_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', $id);
    $stmt->execute();
    $row = $stmt->fetch();

    $content = $row['post_content'];
    ?>
<form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="addpost.php" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="post_title">Post Title</label>
                <input type="text" class="form-control" id="post_title"  name="post_title" placeholder="Post Title" 
                value="<?php
                        if (isset($formdata['post_title']) && !empty($formdata['post_title'])) {
                            echo $formdata['post_title'];
                        } else {
                            echo $row['post_title'];
                        }
                        ?>" required>
            </div>

        <div class="form-group">
        <label for="post_subtitle">Post Subtitle</label>
            <input type="text" class="form-control" id="post_subtitle"  name="post_subtitle" placeholder="Post Subtitle" 
            value="<?php
                    if (isset($formdata['post_subtitle']) && !empty($formdata['post_subtitle'])) {
                        echo $formdata['post_subtitle'];
                    } else {
                        echo $row['post_subtitle'];
                    }
                    ?>" required>
        </div>


        <div class="form-group">
          <label for="post_category">Category</label>
          <select class="form-control" name="post_category" id="post_category" required>
          <option value=''>------- Select --------</option>
            <?php
            $sql = "SELECT * FROM category";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
            <label for="fileToUpload">Profile Picture</label>
            <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload" required>
        </div>

        <div class="form-group">
            <label for="post_content">Post Information</label>
            <textarea class="form-control" id="post_content" name="post_content" rows="3"><?php
                                                                                            if(isset($formdata['post_content']) && !empty($formdata['post_content'])) {
                                                                                                echo $formdata['post_content'];} else {echo $content;
                                                                                            }?></textarea>
        </div>

        <input type="hidden" id="post_id" name="post_id" value="<?php echo $row['post_id']; ?>" />
        <input type="hidden" id="username" name="username" value="<?php echo $row['username']; ?>" />
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>
    <?php

}//end showform
include_once "footer.php";
?>