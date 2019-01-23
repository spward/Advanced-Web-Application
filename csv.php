<?php require_once "connect.php";

header("Content-Transfer-Encoding: ascii");
header("Content-Disposition: attachment; filename=posts.csv");
header("Content-Type: text/comma-separated-values");
?>

category, creator, contents, title, subtitle, date
 
<?php
 
try {
    $sql = "SELECT * from posts INNER JOIN members ON posts.post_creator = members.member_id INNER JOIN category ON posts.post_category = category.category_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p class='error'>ERROR fetching users! " .$e->getMessage() . "</p>";
//    header("Location: forum.php");
    exit();
}
 
foreach ($result as $row) {
    $cleancontents = str_replace(['"', ',', "\r", "\n"], " ", $row['post_content']);
    $cleantitle = str_replace(['"', ',', "\r", "\n"], " ", $row['post_title']);
    $cleansubtitle = str_replace(['"', ',', "\r", "\n"], " ", $row['post_subtitle']);
 
    echo $row['category_name'] . "," . $row['name'] . "," . $cleancontents . "," . $cleantitle . "," . $cleansubtitle . "," . $row['post_date'] . "\n";
}
 
?>