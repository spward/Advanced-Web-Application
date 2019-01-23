<?php
include_once "header.php";
//set initial variables
$errormsg = 0;
$errorcategory = "";

if (!isset($_SESSION['member_id']) && !isset($_session['login'])) {
    $showform = 0;  // show form is true
    header("location: login.php");
} else {
    $showform = 1;  // show form is true
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //create variables to store data from form - we never use POST directly w/ user input
    /* ****** NEW - CHANGED USERNAME TO LOWERCASE ****** */
    $formdata['post_title'] = trim($_POST['post_title']);
    $formdata['post_subtitle'] = trim($_POST['post_subtitle']);
    $formdata['post_content'] = trim($_POST['post_content']);
    $formdata['post_category'] = trim($_POST['post_category']);

    /* ****** NEW - CHECK FOR DUPLICATE ENTRIES ****** */
    try {
        $sqlusers = "SELECT * FROM posts WHERE post_title = :post_title";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':post_title', $formdata['post_title']);
        $stmtusers->execute();
        $countusers = $stmtusers->rowCount();
        if ($countusers > 0) {
            $errormsg = 1;
            echo "<p>The category is already available.</p>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'><p></p>ERROR selecting users!" . $e->getMessage() . "</p></div>";
        exit();
    }

    if ($errormsg == 1) {
        echo "<p class='error'>There are errors.  Please make corrections and resubmit.</p>";
    } else {
        try {
            if ($_FILES["fileToUpload"]["tmp_name"]) {
                require_once "uploadimage.php";
            }

            if ($uploadOk != 0) {
            //query the data
            $sql = "INSERT INTO posts (post_title, post_subtitle, post_creator, post_date, post_content, post_category, post_img) 
                      VALUES (:post_title, :post_subtitle, :post_creator, :post_date, :post_content, :post_category, :post_img)";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':post_title', $formdata['post_title']);
            $stmt->bindValue(':post_subtitle', $formdata['post_subtitle']);
            $stmt->bindValue(':post_creator', $_SESSION['member_id']);
            $stmt->bindValue(':post_date', $current_date);
            $stmt->bindValue(':post_content', $formdata['post_content']);
            $stmt->bindValue(':post_category', $formdata['post_category']);
            $stmt->bindValue(':post_img', basename($target_file));

            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
            echo "<p>Thanks for entering your information.</p>";
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    } // else errormsg
}//submit
if ($showform == 1) {
    ?>


    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="addpost.php" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="post_title">Post Title</label>
                <input type="text" class="form-control" id="post_title"  name="post_title" placeholder="Post Title" 
                value="<?php if (isset($formdata['post_title'])) {
                            echo $formdata['post_title'];
                        } ?>" required>
            </div>

        <div class="form-group">
        <label for="post_subtitle">Post Subtitle</label>
            <input type="text" class="form-control" id="post_subtitle"  name="post_subtitle" placeholder="Post Subtitle" 
            value="<?php if (isset($formdata['post_subtitle'])) {
                        echo $formdata['post_subtitle'];
                    } ?>" required>
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
            <textarea class="form-control" id="post_content" name="post_content" rows="3"><?php if (isset($formdata['post_content'])) { echo $formdata['post_content'];}?></textarea>
        </div>

        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>

    <?php

}//end showform
include_once "footer.php";
?>