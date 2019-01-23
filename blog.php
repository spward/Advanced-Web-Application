<?php
include_once "header.php";
?> <!-- Wrapper -->
			<div id="wrapper">


				<!-- Main -->
					<div id="main">

					<a class="btn btn-info m-3" href="csv.php">Export to CSV</a>
					<a class="btn btn-secondary m-3"" href="blog.php">All Posts</a>
					<?php 
						$sqlcategory = "SELECT * FROM category";
						$stmtcategory = $pdo->prepare($sqlcategory);
						$stmtcategory->execute();
						$category = $stmtcategory->fetchAll(PDO::FETCH_ASSOC);

						foreach ($category as $row) {
							echo "<a class='btn btn-secondary m-3' href=blog.php?cat=" . $row['category_id'] . ">" . $row['category_name'] . "</a>";
						}
						if (isset($_SESSION['member_id']) && isset($_SESSION['login'])) {
							echo "<a class='btn btn-outline-info m-3 float-right' href='addpost.php'>New Blog Post</a>";
						}
					?>


<?php 

try {
	if(!isset($_GET['cat'])){
		$sqlposts = "SELECT * FROM posts INNER JOIN members ON posts.post_creator = members.member_id INNER JOIN category ON posts.post_category = category.category_id";
	} else {
		$sqlposts = "SELECT * FROM posts INNER JOIN members ON posts.post_creator = members.member_id INNER JOIN category ON posts.post_category = category.category_id WHERE post_category = :cat";
	}
	$stmtposts = $pdo->prepare($sqlposts);
	if(isset($_GET['cat'])){
		$stmtposts->bindValue(':cat', $_GET['cat']);
	}
	$stmtposts->execute();
	$result = $stmtposts->fetchAll(PDO::FETCH_ASSOC);



	foreach ($result as $row) {
		$content = substr($row['post_content'], 0, 200);
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
					<a href=href="post.php?id=<?php echo $row['post_id'] ?>" class="image featured"><img src="/uploads/<?php echo $row['post_img'] ?>" alt="" /></a>
					<p><?php echo $content ?>...</p>
					<footer>
						<ul class="actions">
							<li><a href="post.php?id=<?php echo $row['post_id']?>" class="btn btn-outline-primary my-4">Continue Reading</a></li>
						</ul>
						<ul class="stats">
							<?php 
							if($row['twitter'] != NULL){

							?>
							<li><a href="https://twitter.com/<?php echo $row['twitter'] ?>?ref_src=twsrc%5Etfw" class="icon fa-twitter" data-show-count="false">Follow @<?php echo $row['twitter'] ?></a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></li>
							<?php

							}
							?>
							<li><a href="#"><?php echo $row['category_name'] ?></a></li>
							<?php
							if(isset($_SESSION['member_id'])){
							$sqllikes = "SELECT * FROM likes WHERE like_creator = :like_creator AND like_post = :like_post";
							$stmtlikes = $pdo->prepare($sqllikes);
							$stmtlikes->bindValue(':like_creator', $_SESSION['member_id']);
							$stmtlikes->bindValue(':like_post', $row['post_id']);
							$stmtlikes->execute();
							$countlikes = $stmtlikes->rowCount();
							if ($countlikes == 0) {
								echo "<li><a href='#' class='icon fa-heart'>" . $row['post_likes'] . "</a></li>";
							} else if ($countlikes != 0) {
								echo "<li><a href='#' class='icon fa-heart liked'>" . $row['post_likes'] . "</a></li>";
							}

							} else {
								echo "<li><a href='#' class='icon fa-heart'>" . $row['post_likes'] . "</a></li>";
							}
						?>
							<li><a href="#" class="icon fa-comment">128</a></li>
						</ul>
					</footer>
				</article>
<?php
	}

} catch (PDOException $e) {
	echo "<div class='error'><p></p>ERROR selecting users! " . $e->getMessage() . "</p></div>";
	exit();
}


?>

					</div>


            </div>
            
<?php 
include_once "footer.php";
?>