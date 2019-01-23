<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true
$errormsg = 0;
$errorcategory = "";
$id = "";

// Validate they are logged in
if (!isset($_SESSION['member_id']) && !isset($_session['login'])) {
    $showform = 0;  // show form is true
    header("location: login.php");
} else {
    $showform = 1;  // show form is true
} 

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
    $formdata['category_name'] = trim($_POST['category_name']);

    //check for empty fields
    if (empty($formdata['category_name'])) {
        $errorcategory = "The Category Name is required.";
        $errormsg = 1;
    }

        // Check if there is duplicate users
    try {
        $sqlcategory = "SELECT * FROM category WHERE category_name = :category";
        $stmtcategory = $pdo->prepare($sqlcategory);
        $stmtcategory->bindValue(':category', $formdata['category_name']);
        $stmtcategory->execute();
        $countcategory = $stmtcategory->rowCount();
        if ($countcategory > 0) {
            $errormsg = 1;
            echo "<p>The Category Name is already taken.</p>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'><p></p>ERROR selecting users! " . $e->getMessage() . "</p></div>";
        exit();
    }



    if ($errormsg == 1) {
    } else {
        try {
            //query the data
            $sql = "UPDATE category SET category_name = :category WHERE category_id = :ID";
            //prepares a statement for execution
            $stmt = $pdo->prepare($sql);
            //binds the actual value of $_GET['ID'] to
            $stmt->bindValue(':category', $formdata['category_name']);
            $stmt->bindValue(':ID', $id);
            //executes a prepared statement
            $stmt->execute();
            //hide the form
            $showform = 0;
            //provide useful confirmation to user
            echo "<p>Thanks for updating your information.  See your <a href='viewcategory.php?ID={$_POST['ID']}'>updated entry</a>.</p>";
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }//ERRORS
}//POST
if ($showform == 1) {

    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
    $sqlorig = "SELECT * FROM category INNER JOIN members ON category.category_creator = members.member_id";
    $stmtorig = $pdo->prepare($sqlorig);
    $stmtorig->bindValue(':ID', $id);
    $stmtorig->execute();
    $roworig = $stmtorig->fetch();

    if ($_SESSION['member_id'] != $roworig['member_id']) {
        header("location: categories.php");
    }
    
    ?>
    <form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="form" name="form" method="post" action="updatecategory.php" novalidate>
        <div class="form-group">
               <label for="category_name">Category Name</label>
               <input name="category_name" class="form-control" id="category_name" type="text"
                           value="<?php
                                    if (isset($formdata['category_name']) && !empty($formdata['category_name'])) {
                                        echo $formdata['category_name'];
                                    } else {
                                        echo $roworig['category_name'];
                                    }
                                    ?>" required />
        </div>

                <input type="hidden" id="ID" name="ID" value="<?php echo $roworig['category_id']; ?>" />
                <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </form>
    <?php

}//end showform
include_once "footer.php";
?>