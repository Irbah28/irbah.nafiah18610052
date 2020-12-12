<?php 
    require("navbar.php");
?>



<section>
    <br><br>
    <form action="controler/signup.control.logic.php" name='signup'    method="POST"  >
        <br><br><input  name="name"   type="text"      placeholder="Username" > Username
        <br><br><input  name="mail"   type="text"      placeholder="E-mail"   > E-mail
        <br><br><input  name="passwd" type="password"  placeholder="Password" > Password
        <br><br><input  name="passwd2" type="password" placeholder="Password" > Password repeat
        <br><br><input  name="terms"  type="checkbox"                         > Accept terms
        <br><br><button name="signup-submit"  type="submit" id="signup-btn"   >Signup</button>
    </form>
