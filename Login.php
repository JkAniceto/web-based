<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Login </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>

</head>
<body>
<?php include_once('connection.php');?>
<!-- <div class="container"><div class="row justify-content-center">

     <form  method="post" class="text-center col-6 margin_top form1" >
      <img src="settings/logo.png"><br>
      <input class="margin" type="text" name="username" placeholder="Username" ></br>
      <input class="margin" type="password" name="password" placeholder="Password" ></br>
      <button class="margin" type="submit" name="login" value="login">Login</button><br>
      <div class="pass">Forgot Password?</div>
      <div class="signup_link">
        Not a member? <a href="register.php">Signup now</a>
      </div>
    </form> -->

<form method="post">

      <button id="login" class="btn btn-primary btn-sm">Click to Log in</button>
      <section id="modal" class="modal fade">
        <div class="modal-body">
          <span id="close" onclick="close" class="glyphicon glyphicon-remove-circle pull-right"></span>


         <div class="form-group">
             <h1 class="title">Web Based Ordering Management System </h1>
           <div class="data">
            <label for="email">Username</label>
              <input class="margin" type="text" name="username" placeholder="Username" required ></br>
         </div>
         <div class="form-group">
           <div class="data">

            <label for="password">Password</label>
            <input class="margin" type="password" name="password" placeholder="Password" required></br>
            </div>

         </div>

         <div class="pass">Forgot Password?</div>
          <button class="btnsubmit" type="submit" name="login" value="login">Login</button><br>
          <!-- <button class="btn btn-primary btn-sm btn-block" type="submit" name="login" value="login">Login</button> -->
          <div class="signup_link">
            Not a member? <a href="register.php">Signup now</a>
          </div>
        </form>
       </div> <!-- Modal Body -->

      </section>


    </div>

    </form>




    <!-- otp (Bootstrap MODAL) -->
    <div class="modal fade" id="otpModal" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-body">
                    <form method="post" class="form-group">
                        <h3>Please Enter your OTP:</h3>
                        <input type="text" class="form-control" placeholder="otp" name="otp" >
                        <input data-dismiss="modal" class="submit1" type="submit" value="Cancel" name="Cancel">
                        <input class="submit1" type="submit" value="Resend" name="Resend">
                        <input class="submit1" type="submit" value="Verify" name="Verify">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php


        if(isset($_POST['login'])){
            $_SESSION["username"]  = $_POST['username'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            if(empty($username) || empty($password)){
                echo '<script type="text/javascript">alert("Please complete details!");</script>';
                echo "<script>window.location.replace('login.php')</script>";
                return;
            }
            //admin block
            if($_POST['username'] === 'admin'){
                $readQuery = "select * from admin_tb";
                $sql = mysqli_query($conn,$readQuery);
                while($rows = mysqli_fetch_assoc($sql)){
                    $valid = password_verify($password, $rows['password']);
                }
                if($valid)
                    echo "<SCRIPT> location.replace('admin.php');</SCRIPT>";
                else
                    echo "<SCRIPT>  window.location.replace('login.php'); alert('incorrect username or password!');</SCRIPT>";
            }
            else{ //user block
                $readQuery = "select * from user_tb where username = '$username'";
                $result = mysqli_query($conn,$readQuery);
                if(mysqli_num_rows($result) === 1){
                    while($rows = mysqli_fetch_assoc($result)){
                        $valid = password_verify($password, $rows['password'])?true:false;
                        $otp = $rows['otp'];
                        $userlinkId = $rows['userlinkId'];
                    }
                    if($valid && $otp == ""){
                        $_SESSION['userlinkId'] = $userlinkId;
                        echo "<SCRIPT> window.location.replace('homePage.php?username=$username');  </SCRIPT>";
                    }
                    else if($valid && $otp != ""){
                        echo "<script type='text/javascript'>$('#otpModal').modal('show');</script>";
                    }
                    else
                        echo "<SCRIPT>alert('incorrect username or password!');</SCRIPT>";
                }
                else
                    echo "<SCRIPT>alert('incorrect username or password!');</SCRIPT>";
            }
        }
        if(isset($_POST['Verify'])){
            $username = $_SESSION["username"];
            $otp = $_POST['otp'];
            $readQuery = "select * from user_tb where username = '$username' && otp = '$otp' ";
            $result = mysqli_query($conn,$readQuery);
            if(mysqli_num_rows($result) === 1){
                while($rows = mysqli_fetch_assoc($result))
                    $_SESSION['userlinkId'] = $rows['userlinkId'];
                $updateQuery = "UPDATE user_tb SET otp='' WHERE otp='$otp'";
                if(mysqli_query($conn, $updateQuery))
                    echo "<SCRIPT> window.location.replace('homePage.php?username=$username'); </SCRIPT>";
            }else
            echo  '<script type="text/javascript">alert("Incorrect Otp!"); window.location.replace("login.php");</script>';
        }

    ?>


</body>
</html>
<script>


$(document).ready(function(){

$("#login").on('click',function(){

$("#modal").modal({show:true});

});//show modal

$("#close").on('click', function(){

$('#modal').modal('hide');

});//hide modal
});

</script>
<style>

@import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');
body{
  background-color: gray;

}
.title{
  font-weight: bold;
  font-style: italic;
  text-align: center;
}
form .data label{
  font-size: 15px;
  color: black;
  font-weight:bold;
  text-align: justify;
}
form .data input{
  height: 100%;
  width: 100%;
  padding-left: 10px;
  font-size: 17px;
  border: 1px solid red;
  box-shadow: 5px 5px 5px black;
  border-radius: 10px;
}
.modal-body{
  background-color: #55BCCB;
  text-align: justify;
  width: 300px;
  height: 500px;
  box-shadow: 5px 5px 5px black;
  border-radius: 10px;
}
#login {
  margin:0 auto;
  display: block;
  margin-top: 5%;
}

#modal {
  /* width: 500px;
  height: 500px; */
  left: 50%;
  top: 50%;
  margin-left: -150px;
  margin-top: -200px;
}

.btnsubmit{
  background-color: red;
  display: block;
  width: 100%;
  border-radius: 5px;
  border-color: black;
  padding: 10px 25px;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  text-align: center;

}
.btnsubmit:hover{
  background-color: white;
  transition: .2s;
}
/* Signup Link */
.signup_link{
  color: white;
  font-size: 18px;
  text-align: center;
}
.pass{
  text-align: justify;
  color: white;
}
/* otp modal */
.modal-content {
  width: 100% ;
  height: 100%;
  margin-left: -30px;
  margin-top: -10px;
}
.submit1{
  display: block;
  width: 100%;
  border-radius: 5px;
  border-color: black;
  padding: 10px 25px;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  text-align: center;
}


 /* body{
  background-image: url(settings/bg.jpg);
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-position: center;
  max-width: 100%;
  width: auto;
  height: auto;
  font-family: 'Josefin Sans', sans-serif;
} */
/* Restaurant Logo */
 /* img{
  max-width: 50%;
  height: auto;
  width: auto;
} */
/* Container form1 */
 /* .form1{
  background: gray;
  position: absolute;
  top: 48%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 15px 50px 15px;
  height: auto;
  border-radius: 15px;
  /* box-shadow: 5px 7px black; */
} */
/* Username & Password */
/* input[type="text"],
input[type="password"]{
  width: 100%;
  border-radius: 5px;
  border-color: transparent;
  padding: 10px 25px;
  cursor: pointer;
  font-size: 20px;
}
 .margin{
  margin: 10px 0 0;
} */
/* Login Button */
 /* button{
  display: block;
  width: 100%;
  border-radius: 5px;
  border-color: transparent;
  padding: 10px 25px;
  cursor: pointer;
  font-size: 18px;
}
 button:hover{
  border-color: #a6a6a6;
  transition: .2s;
} */
/* Forgot Password? */
 /* .pass{
  color: #a6a6a6;
  cursor: pointer;
  font-size: 18px;
}
 .pass:hover{
  text-decoration: underline;
} */
/* Signup Link */
 /* .signup_link{
  color: white;
  font-size: 18px;
} */


   /* *{
  margin: 0;
  padding: 0;
  outline: none;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

body{
  height: 100vh;
  width: 100%;
  background-color: #E0163D;
}
.show-btn{
  background-color: black;
  padding: 10px 20px;
  font-size: 20px;
  font-weight: 700;
  color: #3498db;
  cursor: pointer;
}
.show-btn, .container{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

input[type="checkbox"]{
  display: none;
}
.container{
  display: none;
  background: #fff;
  width: 410px;
  padding: 30px;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
#show:checked ~ .container{
  display: block;
}
.container .close-btn{
  position: absolute;
  right: 20px;
  top: 15px;
  font-size: 18px;
  cursor: pointer;
}
.container .close-btn:hover{
  color: #3498db;
}
.container .text{
  font-size: 35px;
  font-weight: 600;
  text-align: center;
}
.container form{
  margin-top: -20px;
}
.container form .data{
  height: 45px;
  width: 100%;
  margin: 40px 0;
}
form .data label{
  font-size: 15px;
}
form .data input{
  height: 100%;
  width: 100%;
  padding-left: 10px;
  font-size: 17px;
  border: 1px solid silver;
  box-shadow: 10px 10px 10px black;
}
form .data input:focus{
  border-color: #3498db;
  border-bottom-width: 2px;

}
.pass{
  text-align: center;
}
form .btn{
  margin: 15px 0;
  height: 45px;
  width: 100%;
  position: relative;
  overflow: hidden;
  box-shadow: 10px 10px 19px black;
}
.btn{
  color: black;
  font-size: 20px;
  font-weight: 700;
  cursor: pointer;
}
 .btn:hover{
   background-color: red;
 }

form .btn button{
  height: 100%;
  width: 100%;
  background: none;
  border: none;
  color: #fff;
  font-size: 18px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
}
form .signup-link{
  text-align: center;
}
form .signup-link a{
  color: #3498db;
  text-decoration: none;
}
form .signup-link a:hover{
  text-decoration: underline;
}   */




</style>
