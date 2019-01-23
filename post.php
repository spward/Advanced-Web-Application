<?php require_once "header.php";?>
<!-- Wrapper -->
			<div id="wrapper">


				<!-- Main -->
					<div id="main">

<?php 

try {
	$sqlposts = "SELECT * FROM posts INNER JOIN members ON posts.post_creator = members.member_id INNER JOIN category ON posts.post_category = category.category_id WHERE post_id = :post_id";
	$stmtposts = $pdo->prepare($sqlposts);
	$stmtposts->bindValue(':post_id', $_GET['id']);	
	$stmtposts->execute();
	$result = $stmtposts->fetchAll(PDO::FETCH_ASSOC);

	foreach ($result as $row) {
?>
				<article class="post">
					<header>
						<div class="title">
							<h2><a href="post.php?id=<?php echo $row['post_id']?>"><?php echo $row['post_title'] ?></a></h2>
							<p><?php echo $row['post_subtitle'] ?></p>
						</div>
						<div class="meta">
							<time class="published"><?php echo date("F jS, Y", strtotime($row['post_date'])) ?></time>
							<a href="#" class="author"><span class="name"><?php echo $row['name'] ?></span><img class="profile-pic" src="/uploads/<?php echo $row['img'] ?>" alt="" /></a>
						</div>
					</header>
					<a href="#" class="image featured"><img src="/uploads/<?php echo $row['post_img'] ?>" alt="" /></a>
					<p><?php echo $row['post_content'] ?></p>
					<footer>
						<ul class="stats">
							<li><a href="#"><?php echo $row['category_name'] ?></a></li>
							<li><a href="#" class="icon fa-heart">28</a></li>
							<li><a href="#" class="icon fa-comment">128</a></li>
							<?php 
							if(isset($_SESSION['member_id'])){
								if ($row['post_creator'] == $_SESSION['member_id']) {
									echo "<li><a href='editpost.php?id=" . $row['post_id'] . "'>Edit Post</a></li>";
									echo "<li><a href='deletepost.php?id=" . $row['post_id'] . "'>Delete Post</a></li>";
								}
							}
								
							?>
						</ul>
					</footer>
				</article>
<?php
	}

} catch (PDOException $e) {
	echo "<div class='error'><p></p>ERROR selecting users! " . $e->getMessage() . "</p></div>";
	exit();
} require_once "footer.php"; ?>