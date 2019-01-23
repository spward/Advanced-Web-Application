<?php
  require_once "header.php";
$showform = 1;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $formdata['email'] = trim(strtolower($_POST['email']));
  try{
  $sqlusers = "SELECT * FROM members WHERE email = :email";
  $stmtusers = $pdo->prepare($sqlusers);
  $stmtusers->bindValue(':email', $formdata['email']);
  $stmtusers->execute();
  $row = $stmtusers->fetch();
  $countemail = $stmtusers->rowCount();

    $email = password_hash($formdata['email'], PASSWORD_BCRYPT);
    if ($countemail < 1) {
      echo "<p class='text-danger'>This email cannot be found.</p>";
    } else {
      $link = "<a href='http://ccuresearch.coastal.edu/spward/csci409sp18/changepwd.php?&reset=" . $email . "'>Click To Reset password</a>";
      
      $mail = new PHPMailer(true);
      try {
            //Server settings
        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'spward@coastal.edu';                 // SMTP username
        $mail->Password = 'Swswswsw1';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;  

        //Recipients
        $mail->setFrom('spward@coastal.edu', 'Sean');
        $mail->AddAddress($row['email'], $row['name']);    // Add a recipient

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Reset Password';
        $mail->Body = 'Click On This Link to Reset Password ' . $link . '';

        if ($mail->Send()) {
          echo "Check Your Email and Click on the link sent to your email<br>";
          $showform = 0;
          echo "We will now redirect you to the home page.<br>";
          header("refresh:5;url=index.php");
          echo "<a class='btn btn-primary'>Redirect Now!</a>";
        }
       } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
      }          
      
    }
  } catch (PDOException $e) {
    echo "<div class='text-danger'><p>ERROR selecting users!" . $e->getMessage() . "</p></div>";
    exit();
  }

}
if ($showform == 1) {
?>

    <form class="col-xl-5 col-lg-7 col-md-9 mx-auto mt-5" id="form" name="form" method="POST" action="recoverpwd.php" novalidate>
        <div class="form-group">
            <label for="email">Enter Email Address To Send Password Link</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" 
            value="<?php if (isset($formdata['email'])) {
                    echo $formdata['email'];
                  } ?>" required>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
        </div>
    <div class="my-3">
        <input type="submit" name="submit" id="submit" value="Submit"/>
    </div>
    </form>

<?php
}
  require_once "footer.php";
?>