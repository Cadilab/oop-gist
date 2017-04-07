<?php include('layout/_header.php'); ?>

<div class="jumbotron">
<div class="container">

<h4>Welcome to your test script, please login or register.</h4>

<?php

    if ($login->errors) 
    {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }


    if ($login->messages) 
    {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
?>

<br/><br/>

<form method="POST" action="index.php">
	
	<input type="text" name="username" placeholder="Username"><br/><br/>
	<input type="email" name="email" placeholder="E-mail"><br/><br/>
	<input type="password" name="password" placeholder="Password"><br/></br/>
	<input type="password" name="repassword" placeholder="Re-password"><br/><br/>
	<input type="submit" name="register" value="Register">

</form>

<br/>Or you can login to your account:

<br/><br/><form method="POST" action="index.php">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <input type="submit" name="login" id="login" value="Login">
</form>

<?php include('layout/_footer.php'); ?>