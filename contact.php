<?php

$showform = 1;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "spward@coastal.edu";
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $headers = "From: " . $_POST['email'];

    $sent = mail($to, $subject, $message, $headers);

    if ($sent == 1) {
        echo "<p>The email has been sent!</p>";
    } else {
        echo "<p>There was an error sending your email.</p>";
    }
    $showform = 0;
}

if ($showform==1) {
    require_once "header.php";
?>
<div class="container">
    <p class="h1 text-center pt-5">Contact Us!</p>
    <form class="col-xl-5 col-md-9 mx-auto mt-5" action="contact.php" method="post" name="emailme" id="emailme" novalidate>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
        </div>

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" name="subject" id="subject" required>
            <div class="invalid-feedback">
                Please provide a valid subject.
            </div>
        </div>
        
        <div class="form-group">
            <label for="subject">Message</label>
            <textarea name="message" class="form-control" id="message" cols="30" rows="10" required></textarea>
            <div class="invalid-feedback">
                Please provide a valid subject.
            </div>
        </div>

        <input class="btn btn-primary btn-block mt-3" type="submit" name="submit" id="submit" value="submit"/>
    </form>
</div>
<?php }
    require_once "footer.php";
?>