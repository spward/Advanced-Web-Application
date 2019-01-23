<?php
include_once "header.php";
//set initial variables
$showform = 1;  // show form is true

// Validate they are logged in
if (!isset($_SESSION['member_id']) && !isset($_SESSION['login'])) {
    $showform = 0;  // show form is true
    header("location: login.php");
} else {
    $showform = 1;  // show form is true
} 

//Keeping track of ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    echo "<p class=\"error\">Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //query the data
        $sql = "DELETE FROM posts WHERE post_id = :post_id";
        //prepares a statement for execution
        $stmt = $pdo->prepare($sql);
        //binds the actual value of $_GET['ID'] to
        $stmt->bindValue(':post_id', $id);  //notice this is NOT submitted from the form
        //executes a prepared statement
        $stmt->execute();
        //hide the form
        $showform = 0;
        //provide useful confirmation to user
        header("location: post.php");
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
if ($showform == 1) {
    
    //COLLECT ORIGINAL DATA TO POPULATE THE FORM:
    $sqlorig = "SELECT * FROM posts WHERE post_id = :post_id";
    $stmtorig = $pdo->prepare($sqlorig);
    $stmtorig->bindValue(':post_id', $id);
    $stmtorig->execute();
    $roworig = $stmtorig->fetch();
    ?>
    <div class="text-center">
    <h2 class="m-5">Confirm deletion of <span class="h5 text-primary"><?php echo $roworig['post_title']; ?></span>.</h2>
    <form id="deletestuff" name="deletestuff" method="post" action="deletepost.php">
        <input type="hidden" id="id" name="id" value="<?php echo $roworig['post_id']; ?>" />
        <input class="btn" type="submit" id="delete" name="delete" value="YES" />
        <input class="btn" type="button" id="nodelete" name="nodelete" value="NO" onClick="window.location='post.php?id=<?php echo $roworig['post_id'] ?>'" />
    </form>
    </div>
    <?php

}//end showform
include_once "footer.php";
?>