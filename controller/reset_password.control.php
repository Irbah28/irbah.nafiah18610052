<?php
        require 'vendor/autoload.php';
        
        use Mailgun\Mailgun;
        
        # Instantiate the client.


require("connect.control.db.php");
use AppServer\ConnectDatabase;


class ManageResetPassword extends ConnectDatabase{

   
    function validateEmail($email){
        $link = $this->connect();
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            header("Location: ../forgot_password.php?status=emailError404&" . urlencode($email));
            exit();
        }else{
            $sql="SELECT * FROM users WHERE email=?";
            $stmt = mysqli_stmt_init($link);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../forgot_password.php?status=404&" . urlencode($email));
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($res)){
                if($email == $row['email']){
                    mysqli_stmt_close($stmt);
                    $this->manageonDB($email, $link);
                }
                else{
                    header("Location: ../forgot_password.php?status=emailNotFound&" . urlencode($email));
                    exit();
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        header("Location: ../forgot_password.php?status=emailError&" . urlencode($email));
        exit();
    }


    function manageonDB($email, $link){
        
        $tokenId = bin2hex(random_bytes(8));
        $tokenValidator = bin2hex(random_bytes(32));
        $this->cleanOldUserResetData($link);
        $this->setNewUserResetData($link, $tokenValidator, $tokenId, $email);
        $this->sendEmail($email, $tokenValidator, $tokenId);
        header("Location: ../forgot_password.php?status=success&" . urlencode($email));
        exit();
    }
 
    
    function cleanOldUserResetData($link){

        $sql = "DELETE FROM resetpassw WHERE email=?";
        $stmt = mysqli_stmt_init($link);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=errorClean");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }


    function setNewUserResetData($link, $tokenValidator, $tokenId, $email){
       
        $sql = "DELETE FROM resetpassw WHERE email=?";
        $stmt = mysqli_stmt_init($link);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $expires = date("U") + 800;
        $sql = "INSERT INTO resetpassw(csrfToken, csrfID, email, expires) VALUES(?,?,?,?)";
        $stmt = mysqli_stmt_init($link);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=errorSetPrep");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ssss", $tokenValidator, $tokenId, $email, $expires);
        if(!mysqli_stmt_execute($stmt)){
            echo mysqli_stmt_error($stmt);
            exit();
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);

    }


    function sendEmail($email, $tokenValidator, $tokenId){

        $to = $email;
        $subject = "Reset password Fake Social Network";
        $message = "Hi, click on link bellow to reset your login password \r\n <br>" 
                . "<a href='../reset_password.php?status=reset&tokenID=" . $tokenId . "&tokenValidator=" . $tokenValidator . "'>Reset Password Click Here</a> <br>"
                . "Link will expire after five minutes.<br>";
        $headers = "From: localhost@mail.comm \r\n Content-type: text/html";
        # Include the Autoloader (see "Libraries" for install instructions)

        $Client = new Mailgun('YOUR API');
        $domain = "YOUR DOMAIN";
        # Make the call to the client.
        
        $result = $Client->sendMessage($domain, array(
	        'from'	=> '"From: <localhost@YOUR_DOMAIN.mailgun.org>',
	        'to'	=> 'Baz <' . $to . '>',
	        'subject' => $subject,
            'text'	=> $message,
            'h:header'=> "Content-Type: text/html; charset=ISO-8859-1\r\n",
        ));

        header("Location: ../forgot_password.php?status=success");
        exit();

    }
}


                   
class ResetPassword extends ConnectDatabase{


    function checkResetValidity($values){
        $link = $this->connect();
        $sql = "SELECT * FROM resetpassw WHERE csrfID=?";
        $stmt = mysqli_stmt_init($link);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $values['tokenID']);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if(!$row = mysqli_fetch_assoc($res)){
            header("Location: " . $_SERVER['HTTP_REFERER'] . "&status=resendrequest");
            exit();
        }
        else if ($row['csrfToken'] == $values['tokenValidator'] && $row['expires'] > date("U") && $row['email']){
                $this->reset($link, $row['email'], $values['password']);
        }
        mysqli_stmt_close($stmt);
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&status=resend");
        exit();
    }


    function reset($link, $email, $password){

   
        $sql = "UPDATE users SET passwordUi=? WHERE email=?";
        $stmt = mysqli_stmt_init($link);
        mysqli_stmt_prepare($stmt, $sql);
        $hashedPSW = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ss", $hashedPSW,  $email);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../login.php?status=successReset");
            exit();
        }else
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        
        header("Location: ../reset_Password.php?status=error");
        exit();
    }


}



function eventHandler(){

    if(isset($_POST['request-password-submit'])){
        $reset = new ManageResetPassword();
        $reset->validateEmail($_POST['mail']);

    }
    
    if (isset($_POST['new-password-submit'])){

        if($_POST['password'] != $_POST['password2'] ||  strlen($_POST['password']) < 8 ){
            header("Location: " . $_SERVER["HTTP_REFERER"] . "&status=passwordMiss");
            exit();
        }
        $values = array('tokenID'=>$_POST['tokenID'], 'tokenValidator'=>$_POST['tokenValidator'], 'password'=>$_POST['password']);
        $reset = new ResetPassword();
        $reset->checkResetValidity($values);
    }

    header("Location: " .$_SERVER['HTTP_REFERER']. "?");
    exit();
}


eventHandler()();

?>