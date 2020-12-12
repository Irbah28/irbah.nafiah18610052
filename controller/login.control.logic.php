<?php 

require("connect.control.db.php");
use AppServer\ConnectDatabase;

/* 

AppServer/ConnectDatabase handles connection to Database

*/

class LoginClass extends ConnectDatabase{


    protected $email;
    protected $password;

    function __construct($email, $password){

        $this->email = $email;
        $this->password = $password;
    
    }


    function login(){

        $link = $this->connect() or die("Failed to link.");
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_stmt_init($link);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $this->email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $this->checkLoginAttempt($res);
        header("Location: ../login.php?status=errorSql");
        exit();
    
    }


    function checkLoginAttempt($res){

        if ($res == null){
            header("Location: ../login.php?status=emailError");
            exit();
        }
        if ($row = mysqli_fetch_assoc($res)){ 
            $this->checkPasswords($row);
        }
    }

    
    function checkPasswords($row){

        $passValid = password_verify($this->password, $row['passwordUi']);
        if($passValid == false){
            header("Location: ../login.php?status=passwordError&mail=" . urlencode($this->email));
            exit();
        }else if($passValid == true){
            $this->handleSessions($row);

        }

    }

    function handleSessions($row){

        session_start();
        $_SESSION['UID'] = htmlentities($row['id']);
        $_SESSION['UNAME'] = htmlentities($row['username']);
        header("Location: ../index.php?status=success&name=" . urlencode($row['username'])); 
        exit();

    }


}


/*
Catch user data only under cetrain condition, if request not valid return to common pages
*/
function checkLoginData(){

    if( isset($_POST['login-submit'])){
        if(empty($_POST['mail']) || empty($_POST['passwd'])){
            header("Location: ../login.php?status=incomplete&mail=" . urlencode($_POST['mail']));
            exit();
        }else{
            $values = array("email"=>$_POST['mail'], "password"=>$_POST['passwd']);
            $login = new LoginClass($values['email'], $values['password']);
            $login->login();
        }
    }else{
        header("Location: ../login.php?status=NoLogin");
        exit();
    }
}

checkLoginData();



?>