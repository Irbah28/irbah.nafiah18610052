<?php
    require("navbar.php");
?>


<section>
<h5>Reset password </h5>

    <?php 
        $tokenID = $_GET['tokenID'];
        $tokenValidator = $_GET['tokenValidator'];
         

        if(empty($tokenID) || empty($tokenValidator || $_GET['status'] == "error")){
            echo "<p>Link is expired or broken, send a new request to your email account.</p> <br>
            <a href='forgot_password.php'>Resend Request</a>";
        } 
        if( ctype_xdigit($tokenValidator ) != false && ctype_xdigit($tokenID) != false){
          ?>
            <form action='controler/reset_password.control.php' method='POST'>
            <input type='hidden' name='tokenID' value='<?php echo $tokenID; ?>'/>
            <input type='hidden' name='tokenValidator' value='<?php echo $tokenValidator; ?>'/>
            <input type='password' name='password' placeholder='Enter new password'> 
            <br>
            <br>
            
            <input type='password' name='password2' placeholder='Repeat new password'>
            <br>
            <br>
            <button type='submit' name='new-password-submit'>Save New Passsword</button>
        </form>

    <?php

    }
    ?>

</section>
