<?php
include_once "header.php";
//set initial variables
$errormsg = 0;
$errorcategory = "";

if( !isset($_SESSION['member_id']) && !isset($_session['login'])) {
    $showform = 0;  // show form is true
    header("location: login.php");
} else {
    $showform = 1;  // show form is true
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //create variables to store data from form - we never use POST directly w/ user input
    /* ****** NEW - CHANGED USERNAME TO LOWERCASE ****** */
    $formdata['category'] = trim(strtolower($_POST['category']));


    //check for empty fields
    if (empty($formdata['category'])) {
        $errorcategory = "The Category name is required.";
        $errormsg = 1;
    }

    /* ****** NEW - CHECK FOR DUPLICATE ENTRIES ****** */
    try {
        $sqlusers = "SELECT * FROM category WHERE category_name = :category";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':category', $formdata['category']);
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
            //query the data
            $sql = "INSERT INTO category (category_name, category_date, category_creator) 
                      VALUES (:category, :date, :creator)";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':category', $formdata['category']);
            $stmt->bindValue(':date', $current_date);
            $stmt->bindValue(':creator', $_SESSION['member_id']);
            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
            echo "<p>Thanks for entering your information.</p>";
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    } // else errormsg
}//submit
if ($showform == 1) {
    ?>


    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="addcategory.php" novalidate>
        <div class="form-group">
            <label for="category">Category Name</label>
                <input type="text" class="form-control" id="category"  name="category" placeholder="Category Name" 
                value="<?php if (isset($formdata['category'])) {echo $formdata['category'];} ?>" required>
            </div>
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>

    <?php

}//end showform
include_once "footer.php";
?>