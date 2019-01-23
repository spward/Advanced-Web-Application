<?php

require_once "header.php";
try {
    if (!isset($_SESSION['member_id']) && !isset($_session['login'])) {
        $showform = 0;  // show form is true
        header("location: login.php");
    } else {
        $showform = 1;  // show form is true
    } 

    $sql = "SELECT * FROM category INNER JOIN members ON category.category_creator = members.member_id WHERE category_id = :ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":ID", $_GET['ID']);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo
        "<table class='my-5 col-4 mx-auto'>
          <tr><th>Category ID</th> <td>" . $row['category_id'] . "</td></tr>";
    echo "<tr><th>Category Name</th> <td>" . $row['category_name'] . "</td ></tr>";
    echo "<tr><th>Creation Date</th> <td> " . date("F dS, Y", strtotime($row['category_date'])) . "</td></tr>";
    echo "<tr><th>Creator</th> <td> " . $row['name'] . "</td></tr></table>";

} catch (PDOException $e) {
    die($e->getMessage());
}
require_once "footer.php";
?>