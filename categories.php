<?php

require_once "header.php";
?>
<!-- Wrapper -->
			<div id="wrapper">


				<!-- Main -->
					<div id="main">
<?php
try {
    if (!isset($_SESSION['member_id']) && !isset($_session['login'])) {
        $showform = 0;  // show form is true
        header("location: login.php");
    } else {
        $showform = 1;  // show form is true
    }
            echo "<a class='btn btn-outline-info m-3 float-right' href='addcategory.php'>New Category</a>";


    if($_SESSION['role'] == 1) {
        $sql = "SELECT * FROM category ORDER BY category_name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table class='col-3 mx-auto my-5 category_table'><tr class='h5'><th>Categories</th><th>Options</th></tr>";
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row['category_name'] . "</td>";
            echo "<td><a href=viewcategory.php?ID=" . $row['category_id'] . ">View Details</a>";
            echo "<a href=updatecategory.php?ID=" . $row['category_id'] . ">Update Information</a>";
            echo "<a href=deletecategory.php?ID=" . $row['category_id'] . ">Delete Information</a></tr>\n";
        }
        
    } else {
        $sql = "SELECT * FROM category INNER JOIN members ON category.category_creator = members.member_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":ID", $_SESSION['member_id']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table class='col-3 mx-auto my-5 category_table'><tr><th>Categories</th><th>Options</th></tr>";

        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row['category_name'] . "</td>";
            echo "<td><a href=viewcategory.php?ID=" . $row['category_id'] . ">View Details</a>";
            if($row['member_id'] == $_SESSION['member_id']){
                echo "<a href=updatecategory.php?ID=" . $row['category_id'] . ">Update Information</a></tr>\n";
            }
        }
    }

    echo "</table>";
} catch (PDOException $e) {
    die($e->getMessage());
}
?>
</div>
</div>
<?php
require_once "footer.php";
?>