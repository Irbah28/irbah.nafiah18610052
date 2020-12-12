<?php
    include("navbar.php");
?>




<section>
    <div>
        <h4>Details</h4>
        <?php
            echo "<p>" . $_SESSION['UNAME'] . "</p>";
        ?>
    </div>
    <br>
    <p>Edit</p>
    <?php
        $status = $_GET['status'];
        $form = $_GET['form'];
        $error = $_GET['error'];

        if( $status == "success" && $form == "username"){
            
            echo "<p>Username changed.</p>";

        }else if ($status == "error"){
            if($error == "auth"){
                echo "<p>Request is invalid. Issue is with your password.</p>";

            }
            else if($error == "sql"){
                echo "<p>Request is invalid. SQL issue, try again.</p>";

            }
            else if($error == "unique"){
                echo "<p>Request is invalid. Email you are trying to use is already taken.</p>";

            }else{

                echo "<p>Some event have prevented requested action.</p>";
            }

        }
    
    ?>
<form action="controler/edit_user.control.php" method="POST">
    <p>Change Username</p>
    <input type="hidden" name='old-name' value=' <?php echo $_SESSION['UNAME']; ?>'/>
    <input type="text" name='name' placeholder="Enter new username"/><br>
    <br>
    <p>Confirm your authorization</p>
    <input type="password" name="passwd" id="" placeholder="Enter password"/>
    <br>
    <br>
    <button type="submit" name='edit-username-submit'>Save</button>
</form>    
<hr>

<form action="controler/edit_user.control.php" method="POST">

    <p>Change email</p>
    <p>Enter new email</p>
    <input type="hidden" name='old-name' value=' <?php echo $_SESSION['UNAME']; ?>'/>
    <input type="text" name='mail' placehodler='Enter new email'>
    <br>
    <p>Confirm your authorization</p>
    <input type="password" name="passwd" id="" placeholder="Enter password">
    <br>
    <br>
    <button type="submit" name='edit-email-submit'>Save</button>
</form>    
<hr>

    <p>Change Password</p>
    <a href='forgot_password.php'>Change password</a>


</section>
