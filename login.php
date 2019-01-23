<?php

include_once "header.php";

$errormsg = 0;
if(!isset($_SESSION['timeout'])){
    $showform = 1;
} else {
    $showform = 0;
    if (isset($_SESSION['timeout']) && (time() - $_SESSION['timeout'] > 30)) {
        // last request was more than 30 minutes ago
        session_unset();
        session_destroy();
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
//create variables to store data from form - we never use POST directly w/ user input
    /* ****** NEW - CHANGED USERNAME TO LOWERCASE ****** */
    $formdata['login'] = trim(strtolower($_POST['login']));
    $formdata['password'] = trim($_POST['password']);

    require_once "attempts.php";
        
    try {
        $sqlusers = "SELECT * FROM members WHERE username = :login OR email = :login";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':login', $formdata['login']);
        $stmtusers->execute();
        $row = $stmtusers->fetch();
        $countusers = $stmtusers->rowCount();
        if ($countusers < 1) {
            echo "<p class='text-danger'>This user cannot be found.</p>";
        } else {
            if (password_verify($formdata['password'], $row['password'])) {
                $_SESSION['member_id'] = $row['member_id'];
                $_SESSION['login'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];
                
                $showform = 0;
                header("Location: index.php");
            } else {
                echo "<p class='text-danger'>The username and password combination you entered is not correct. Please try again.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "<div class='text-danger'><p>ERROR selecting users!" . $e->getMessage() . "</p></div>";
        exit();

    }
}
if($showform == 1) {

?>

<form class="col-xl-3 col-lg-5 col-md-6 mx-auto mt-5" id="login" name="login" method="post" action="login.php" novalidate>
    <div class="form-group">
        <label for="login">Username or Email</label>
        <div class="input-group">
            <input type="text" class="form-control" id="login"  name="login" placeholder="login" value="<?php if (isset($formdata['login'])) {
                                                                                                                    echo $formdata['login'];
                                                                                                                } ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        </div>
    </div>

    <div class="my-3">
        <input class="btn btn-primary btn-block mt-4" type="submit" name="submit" id="submit" value="Submit"/>
    </div>

    <small><a href="register.php" class="text-muted mt-3 float-left register">Create new Account</a></small>
    <small><a href="recoverpwd.php" class="text-muted mt-3 float-right">Forgot Password?</a></small>
</form>

<?php

}
include_once "footer.php";
?>