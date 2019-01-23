<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true

// Validate they are logged in
if (!isset($_SESSION['member_id']) && !isset($_SESSION['login']) || $_SESSION['role'] != 1) {
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

$sqlorig1 = "SELECT * FROM posts INNER JOIN category ON category.category_id = posts.post_category WHERE post_category = :ID";
$stmtorig1 = $pdo->prepare($sqlorig1);
$stmtorig1->bindValue(':ID', $id);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //query the data
        $sql = "DELETE FROM category WHERE category_id = :ID";
        //prepares a statement for execution
        $stmt = $pdo->prepare($sql);
        //binds the actual value of $_GET['ID'] to
        $stmt->bindValue(':ID', $id);  //notice this is NOT submitted from the form
        //executes a prepared statement
        $stmt->execute();
        //hide the form
        $showform = 0;
        //provide useful confirmation to user
        header("location: categories.php");
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
$sqlorig = "SELECT *, COUNT(post_category) FROM category INNER JOIN posts ON category.category_id = posts.post_category WHERE post_category = :ID";
$stmtorig = $pdo->prepare($sqlorig);
$stmtorig->bindValue(':ID', $id);
$stmtorig->execute();
$roworig = $stmtorig->fetch();
$countposts = $stmtorig1->rowCount();
if ($roworig['COUNT(post_category)'] > 0) {
    $showform = 0;
    echo "cannot delete a category with blog posts.";
}
if ($showform == 1) {
    


    ?>
    <div class="text-center">
    <h2 class="m-5">Confirm deletion of <span class="h5 text-primary"><?php echo $roworig['category_name']; ?></span> Category.</h2>
    <form id="deletestuff" name="deletestuff" method="post" action="deletecategory.php">
        <input type="hidden" id="ID" name="ID" value="<?php echo $roworig['category_id']; ?>" />
        <input class="btn" type="submit" id="delete" name="delete" value="YES" />
        <input class="btn" type="button" id="nodelete" name="nodelete" value="NO" onClick="window.location='categories.php'" />
    </form>
    </div>
    <?php

}//end showform
include_once "footer.php";
?>