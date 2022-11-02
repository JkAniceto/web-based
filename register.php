<!DOCTYPE html>
<html>
    <head>
        <title>Signup</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"></head>
    </head>
    <body>
    <div class="container text-center">

    <form method="post" class="text-center col-6 margin_top form2">
        <h1> Web Based Ordering Management</h1>
        <!-- <img src="settings/logo.png"><br> -->
        <input type="text" class="form" name="username" placeholder="Enter Username" required><br>
        <input type="text" class="form" name="name" placeholder="Enter Name" required><br>
        <input type="email" class="form" name="email" placeholder="Enter Email" required><br>
        <input type="password" class="form" name="password" placeholder="Enter Password" required><br>

        <button type="submit" class="button" name="createAccount">Sign Up</button><br>
        <button type="button" class="button2" id="back">Back</button><br>
        <div class="login_link">
          Have already an account? <a href="Login.php">Login</a>
        </div>

    </form>
    </div>
    </body>
</html>
<script>
    document.getElementById("back").addEventListener("click",function(){
        window.location.replace('login.php');
    });
</script>
<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    if(isset($_POST['createAccount'])){
        $username = $_POST['username'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        if(empty($name) || empty($email) || empty($password)){
        echo "<script>alert('Please complete the details!'); window.location.replace('register.php');</script>";
        return;
        }
        include_once('connection.php');
        $otp = uniqid();
        $hash = password_hash($password, PASSWORD_DEFAULT);
          //Load Composer's autoloader
          require 'vendor/autoload.php';
          //Create an instance; passing `true` enables exceptions
          $mail = new PHPMailer(true);
          try {
              //Server settings
              $mail->SMTPDebug  = SMTP::DEBUG_OFF;
              //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
              $mail->isSMTP();                                            //Send using SMTP
              $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
              $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
              $mail->Username   = 'webBasedOrdering098@gmail.com'; //from //SMTP username
              $mail->Password   = 'cgzyificorxxdlau';                     //SMTP password
              $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;           //Enable implicit TLS encryption
              $mail->Port       =  465;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

              //Recipients
              $mail->setFrom('webBasedOrdering098@gmail.com', 'webBasedOrdering');
              $mail->addAddress("$email");                                //sent to

              //Content
              $mail->Subject = 'OTP';
              $mail->Body    = $otp;

              $mail->send();

          }catch (Exception $e) {
              echo "<script>alert('Error: $mail->ErrorInfo');</script>";
              return;
          }

      if(mysqli_query($conn,"insert into user_tb(username, name, email, otp, password) values('$username','$name','$email','$otp','$hash')"))
          echo "<script>window.location.replace('login.php'); alert('OTP sent please verify your account first!');</script>";
      else
          echo '<script type="text/javascript">alert("failed to save to database");window.location.replace("login.php");</script>';


    }
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');

body{

  /* background-image: url(settings/bg.jpg); /* #ED212D */
  /* background-repeat: no-repeat;
  background-attachment: fixed;
  background-position: center;  */
  background-color: #EDC7B7;
  max-width: 100%;
  width: auto;
  height: auto;
  font-family: 'Josefin Sans', sans-serif;
  color: white;
}
/* Restaurant Logo */
/* img{
  max-width: 100%;
  height: 30%;
  width: 30%;
} */

/* Container form2 */
.form2{
  /* background: gray;
  position: absolute;
  top: 48%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 15px 50px 15px;
  height: auto;
  border-radius: 15px;
  /* box-shadow: 5px 7px #6b0e13; */
  top: 48%;
  left: 50%;
  text-align: justify;
  box-shadow: 5px 5px 5px black;
  padding: 15px 50px 15px;
  transform: translate(-50%, -50%);
  height: auto;
  position: absolute;
  background-color: #2C3531;
}

/* Username, Name, Email, Password */
.form{
  width: 70%;
  border-radius: 5px;
  border-color: transparent;
  padding: 5px 25px;
  cursor: pointer;
  font-size: 20px;
  margin: 10px 0 0;
  box-shadow: 5px 5px 3px black ;
  height: 100%;

}

/* Sign Up and Back Button */
button{
  width: 70%;
  border-radius: 5px;
  border-color: red;
  padding: 5px;
  cursor: pointer;
  font-size: 18px;
  margin: 10px 0 0;

}
button:hover{
  border-color: black;
  transition: .2s;
}
/* Back Button */
.button2{
  /* display: block; */
  width: 70%;
}
/* Login Link */
.login_link{
  font-size: 18px;
}
</style>
