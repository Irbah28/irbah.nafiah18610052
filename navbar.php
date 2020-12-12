<?php
session_start();
?>
<link rel='stylesheet' href='css/base.css'/>


<div id='navbar'>

    <a href="index.php">Home</a>
    <a href="about.php">About</a>

    <?php
    if($_SESSION['UNAME']){
        echo "    <a href='profile.php'>Profile</a>";

        echo "    <a href=''><form  action='controler/logout.control.logic.php' method='POST'> <button name='logout-submit' type='submit'> Logout </button></form></a>";

        echo "    <br><a href=''><p>Logged as " . htmlentities($_SESSION['UNAME'], ENT_QUOTES, 'UTF-8') . "</p></a>";

    }else{
        echo "    <a href='signup.php'>Signup</a>    ";
        echo "    <a href='login.php'>Login</a>    ";
        echo "Logged out";
    }

    ?>
</div>
<header>
    <h3>Fake social network</h3>
</header>

<hr>
<br>

