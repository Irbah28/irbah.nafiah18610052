<?php 
    require("navbar.php");
    if($GET['success'] == "succesReset"){
        echo "<p>Your password have been updated.</p>";
    }
?>

<section>
    <br><br>
    <form action="controler/login.control.logic.php" name='login'    method="POST"  >
        <br><br><input  name="mail"   type="text"      placeholder="E-mail"   > E-mail
        <br><br><input  name="passwd" type="password"  placeholder="Password" > Password
        <br><br><button name="login-submit"  type="submit" id="login-btn" >Login</button>
    </form>
     <a href='forgot_password.php'>I forgot password</a>
</section>
