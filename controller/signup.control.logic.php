<?php

require("connect.control.db.php");
use AppServer\ConnectDatabase;


class User extends ConnectDatabase{


    protected $username;
    protected $password;
    protected $email;

    function __construct($username, $password, $email){

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;

    }


    function createUser($link){

        $sql = "SELECT * FROM `users` WHERE email=?";
        $stmt = mysqli_stmt_init($link) or die("Failed to connect.");
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../signup.php?status=SQLError");
            exit();
        }else{
            mysqli_stmt_bind_param($stmt, "s", $this->email ) or die("Failed bind.");
            mysqli_stmt_execute($stmt) or die("Failed to execute.");
            mysqli_stmt_store_result($stmt);
            $res = mysqli_stmt_num_rows($stmt);
            if($res > 0){
                header("Location: ../signup.php?status=taken&name=" . urlencode($this->username));
                exit();
            } else{
                $hashPSWD = password_hash($this->password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users(username, passwordUi , email) VALUES(?,?,?)";
                $stmt = mysqli_stmt_init($link);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $this->username, $hashPSWD, $this->email);
                if(mysqli_stmt_execute($stmt)){
                  header("Location: ../login.php?status=success&mail=". urlencode($this->email)); 
                }else{
                  header("Location: ../signup.php?status=failed&mail=". urlencode($this->email));
                }
                mysqli_stmt_close($stmt);
                mysqli_close($link);
            }
            exit();
        }
    }
}



function checkFormValues(){
    
    /*
    Check that required data exist and that data is in right format.
    */
    if(isset($_POST['signup-submit'])){
         $values = array('username'=>$_POST['name'], 'password'=>$_POST['passwd'], 'password2'=>$_POST['passwd2'], 'email'=>$_POST['mail']);
         validateFormValues($values);
         $user = new User($values['username'], $values['password'], $values['email']);
         $link = $user->connect() or die("Failed to link.");
         $user->createUser($link) or die('Failed to create.');
     }else{
         header("Location: ../signup.php");
         exit();
     }
}


function validateFormValues($values){

    if(empty($values['username']) || !ctype_alnum($values['username']) || empty($values['password']) || empty($values['password2']) || empty($values['email']) ){
        header("Location: ../signup.php?status=incomplete&name=" 
        . urlencode($values['username']) . "&mail=" . urlencode($values['email'])); 
        exit();
    }else if(strlen($values['password']) < 8 ){
        header("Location: ../signup.php?status=passwordShort&name=" . urlencode($values['username']) 
        . "&mail=" . urlencode($values['email']));
        exit();
    }
    else if(!filter_var($values['email'], FILTER_VALIDATE_EMAIL)){
        header("Location: ../signup.php?status=mailError&name=" . urlencode($values['username'])); 
        exit();
    }
    else if($values['password'] == $values['password2']){ 
        return $values; 
    }else{
        header("Location: ../signup.php?status=passwordMatch&name=" . urlencode($values['username']) 
        . "&mail=" . urlencode($values['email']));
        exit();
    }
}


checkFormValues();



?>
