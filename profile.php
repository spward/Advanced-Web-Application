<?php
include_once "header.php";

if (!isset($_SESSION['member_id']) && !isset($_session['login'])) {
    $showform = 0;  // show form is true
    header("location: login.php");
} else {
    $showform = 1;  // show form is true
} 

try {

    // Display the details of the profile
    $sql = "SELECT * FROM members WHERE member_id = :member_id";
    //prepares a statement for execution
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":member_id", $_SESSION['member_id']);
    //executes a prepared statement
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);



    echo "<div class='text-center'>";
    echo "<h1 class='my-5'>Welcome back, <span class='text-uppercase text-primary'>" . $row['name'] . "</span></h1>";
    if (is_null($row['img'])){
        echo "<img class='profile-pic mx-auto my-5 d-block' src='images/images.jpg'/>";
    } else {
        echo "<img class='profile-pic mx-auto my-5 d-block' src='/uploads/" . $row['img'] . "'/>";    
    }
    
    echo "<table border='1' class=' my-4 mx-auto col-6'>";
    echo    "<tr><th>Full Name</th> <td>" . $row['name'] . "</td ></tr>";
    echo    "<tr><th>Username</th> <td> " . $row['username'] . "</td></tr>";
    echo    "<tr><th>Email</th> <td> " . $row['email'] . "</td></tr></table> <br>";

        echo "<a class='btn btn-primary' href='editprofile.php?ID=" . $_SESSION['member_id'] . "'>Update Account Information</a>
              <a class='btn btn-primary' href='updatepass.php?ID=" . $_SESSION['member_id'] . "'>Update Password</a>  <br></div>";

} catch (PDOException $e) {
    die($e->getMessage());
}
include_once "footer.php";
?>