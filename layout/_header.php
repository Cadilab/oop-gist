<!DOCTYPE html>
<html lang="en">
<head>
	<title>Welcome</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

	<meta name="description" content="Free Web tutorials">
	<meta name="keywords" content="HTML, CSS, XML, JavaScript, PHP, Blog, Dynamic">
	<meta name="author" content="Nebojsa Milinkovic">

</head>
	<nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
	  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	      <li class="nav-item active">
	        <a class="nav-link" href="">Home <span class="sr-only">(current)</span></a>
	      </li>
	    </ul>

	    <?php

	    	if($login->loggedIn() == true)
	    	{
	    		echo '
	    		    <form class="form-inline my-2 my-lg-0">
				      	<input class="form-control mr-sm-2" type="text" placeholder="Search">
				    </form>

		    		<ul class="navbar-nav mr-sm-2">
		    			<a class="nav-link" href="logout.php">Logout</a>
		    		</ul>
	    		';
	    	}

	    ?>

	  </div>
	</nav>



<body>
