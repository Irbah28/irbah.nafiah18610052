<?php
    require("navbar.php");
?>


<section>

    <h5>Request new password </h5>

    <?php
        if($_GET['status'] == "success"){
        
            echo "<p style='color:red;'>Reset password request is sent on your email account.</p><hr><br>";
        }
    ?>
    <form action="controler/reset_password.control.php" method="POST">
        <input type="text" name="mail" placeholder="Enter account email"> 
        <br>
        <br>
        <button type="submit" name='request-password-submit'>Send </button>
    </form>


</section>
