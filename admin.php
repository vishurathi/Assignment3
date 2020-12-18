<?php
    session_start();
    if(!$_SESSION['admin']){
        header("Location: assignment.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="admin.css">

</head>
<body>

    <div class="container">    
            <form method="post">
                <label for="title">
                    <input type="text" placeholder="Title" id="title" name="title">
                </label>
                <br/>

                <label for="user">
                    <input type="text" placeholder="User Name" id="user" name="user">
                </label>
                <br/>

                <label for="pass1">
                    <input type="password" placeholder="Password" id="pass1" name="password"><br>
                    <button id="btn1" >Show/Hide Password</button> 
                </label>
                <br/>

                <label for="pass2">
                    <input type="password" placeholder="Confirm Password" id="pass2"><br>
                    <button id="btn2" name="Quality Check" >Show Strenghth</button>
                </label>
                <br/>

                <p id="notmatch" style="display:none;">Sorry, password does not match</p>

                <label for="quality">
                </label>
                <div class="progress">
                    <div class="progress-bar bg-warning" id="progress" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="50"></div>
                </div>
                <br/>

                <label for="url">
                    
                    <input type="text" placeholder="URL" name="url">
                </label>
                <br>
                <input type="submit" name="submit" id="submit">

            </form>
    </div>


    <?php

                    
            include('config.php');
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'secret key';
            $secret_iv = 'secret iv';
            $key = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            if(isset($_POST['submit'])){
                $title = $_POST['title'];
                $user = $_POST['user'];
                $password = $_POST['password'];
                $url = $_POST['url'];
                
                $output = openssl_encrypt($password, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);

                $query = "insert into data values ('$title','$user','$output','$url')";
                $result = mysqli_query($con,$query);
            

            }


            //showing the table from database.


            $table = "<table border=1>
                <tr>
                    <th>Title</th>
                    <th>User</th>
                    <th>Password</th>
                    <th>URL</th>
                </tr>    

            ";

            $query = "select * from data";
            $result = mysqli_query($con,$query);

            while($row = mysqli_fetch_assoc($result)){
                $title = $row['title'];
                $user = $row['user'];
                $encrypted_password = $row['password'];
                $url = $row['url'];

                $decryptPassword = openssl_decrypt(base64_decode($encrypted_password), $encrypt_method, $key, 0, $iv);

                $table  .= "
                    <tr>
                        <td>$title</td>
                        <td>$user</td>
                        <td>$decryptPassword</td>
                        <td>$url</td>
                    </tr>
                
                ";
            }

            $table .= "</table>";
            echo $table;
            


    ?>

<form method="post">
    <input type="submit" name="logout" value="Logout" id="logout">
</form>

<?php 
    if(isset($_POST['logout'])){
        session_destroy();
        header("Location: assignment.php");
    }

?>

<script>

    document.querySelector("#btn1").addEventListener("click",function(event){
        event.preventDefault();
        var checkAttribute = document.getElementById("pass1").getAttribute("type");
        if(checkAttribute =="text"){
            document.getElementById("pass1").setAttribute("type","password");
        }else{
            document.getElementById("pass1").setAttribute("type","text");
        }
    })

    document.querySelector("#btn2").addEventListener("click",function(event){
        event.preventDefault();
        var checkAttribute = document.getElementById("pass2").getAttribute("type");
        if(checkAttribute =="text"){
            document.getElementById("pass2").setAttribute("type","password");
        }else{
            document.getElementById("pass2").setAttribute("type","text");
        }
    })

    document.querySelector("#pass2").addEventListener("change",function(event){
        event.preventDefault();
        var value1 = event.target.value.toString();
        var value = document.getElementById("pass1").value;
        console.log(value)
        var hasUpper = false;
        var hasNumber = false;

        for(var i=0;i<value.length;i++){
            if(value[i] == value[i].toUpperCase()){
                
                hasUpper = true;
            }else if(value[i] >= "0" || value[i]<= "9"){
                hasNumber = true;
            }
        }
        console.log(value.length,hasUpper,hasNumber);


        if(value == value1){
            if(value.length >= 8 && hasUpper == true && hasNumber == true){
          document.getElementById("progress").style.width="100%";
          document.getElementById("progress").classList.remove("bg-warning")
          document.getElementById("progress").classList.add("bg-success")
        }else if(value.length >= 8 && hasUpper == false && hasNumber == true){
            document.getElementById("progress").style.width="70%";
          document.getElementById("progress").classList.remove("bg-warning")
          document.getElementById("progress").classList.add("bg-warning")
        }else if(value.length >= 8 && hasUpper == true && hasNumber == false){
            document.getElementById("progress").style.width="50%";
          document.getElementById("progress").classList.remove("bg-warning")
          document.getElementById("progress").classList.add("bg-warning")
        }else{
            document.getElementById("progress").style.width="30%";
          document.getElementById("progress").classList.remove("bg-warning")
          document.getElementById("progress").classList.add("bg-danger")
        }

        document.getElementById("submit").disabled = false;
        }else{
            document.getElementById("notmatch").style.display="block";
            document.getElementById("submit").disabled = true;
        }
        


    })



    

</script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
</body>
</html>