<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment</title>
    <link rel="stylesheet" href="admin.css">

</head>
<body >
    <nav class="container">
<form method="post">
<label for="master">
    <input type="password" name="password" id="master" placeholder="Enter Master Password"> <br>
    <input type="submit" value="Login" name="submit">
</label>
</form>
</nav>

<?php 
    include('config.php');    
    if(isset($_POST['submit'])){
        $password = $_POST['password'];
        $query = "select * from master_password";
        $result = mysqli_query($con,$query);
        while($row = mysqli_fetch_assoc($result)){
            if($row['password'] == $password){
                session_start();
                $_SESSION['admin'] = true;
                header("Location: admin.php");
            }else{
                session_start();
                $_SESSION['admin'] = false;
                echo "Please check your password.";
            }
        }

    }



?>
    
</body>
</html>