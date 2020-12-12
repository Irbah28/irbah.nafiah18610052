<?php
    session_start();
    require("connect.control.db.php");

    use AppServer\ConnectDatabase;


    class EditUserDetails extends ConnectDatabase{

        


        function authorizationPSW($values){

            $link = $this->connect();
            $sql = "SELECT * FROM users WHERE username=?";
            $stmt = mysqli_stmt_init($link);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                header('Location: ../profile.php?status=error&error=auth&form=username');
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $values['old-username']);
            if(mysqli_stmt_execute($stmt)){
                $res = mysqli_stmt_get_result($stmt);
                if($row = mysqli_fetch_assoc($res)){
                    $passValid = password_verify($values['password'], $row['passwordUi']);
                    if($passValid == true){
                        $email = $row['email'];
                        mysqli_stmt_close($stmt);
                        mysqli_close($link);
                        return $email;
                    }else{
                        header('Location: ../profile.php?status=error&error=auth');
                        exit();
                    }
                }else{
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    return false;
                }
            }
        }


        function validateEmail($values){

            $link = $this->connect();
            $sql = "SELECT * FROM `users` WHERE email=?";
            $stmt = mysqli_stmt_init($link) or die("Failed to connect.");
            if(!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../signup.php?status=error&error=sql");
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "s", $values['email']) or die("Failed bind.");
                mysqli_stmt_execute($stmt) or die("Failed to execute.");
                mysqli_stmt_store_result($stmt);
                $res = mysqli_stmt_num_rows($stmt);
                if($res > 0){
                    header("Location: ../profile.php?status=error&error=unique");
                    exit();
                }
            }
        }

 
        function editUser($values, $updateField, $updateValue){
           
            $this->validateEmail($values);
            $email = $this->authorizationPSW($values);
            if($email != false){
                $link = $this->connect();
                $sql = "UPDATE `users` SET " . $updateField . "=? WHERE email=?";
                $stmt = mysqli_stmt_init($link);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    header('Location: ../profile.php?status=error&error=sql&form=username');
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "ss", $updateValue, $email);

                if(mysqli_stmt_execute($stmt)){
                    if($updateField == "username"){
                        $_SESSION['UNAME'] = $values['username'];
                    }
                    header('Location: ../profile.php?status=success&error=sql&form=username');
                    exit();
                }

                mysqli_stmt_close($stmt);
                mysqli_close($link);

            }else{
                header("Location: ../profile.php?status=error&error=auth");
                exit();
            }
        }

    }


function getData(){
    
    if (isset($_POST['edit-username-submit'])){

        $name=str_replace(' ', '', $_POST['old-name']);
        $values = array('old-username'=>$name, 'username'=>$_POST['name'], 'password'=>$_POST['passwd']);
        $edit = new EditUserDetails();            
        $edit->editUser($values, 'username', $values['username']);

    }

    if (isset($_POST['edit-email-submit'])){
        $name=str_replace(' ', '', $_POST['old-name']);
        $values = array('old-username'=>$name, 'email'=>$_POST['mail'], 'password'=>$_POST['passwd']);
        $edit = new EditUserDetails();
        $edit->editUser($values, 'email', $values['email']);

    }
    header("Location: ../profile.php");
    exit();

}

getData();

?>