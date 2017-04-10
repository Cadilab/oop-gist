<?php include('layout/_header.php'); ?>

<div class="container">

	<?php 



		echo '

		<br>Welcome '.$_SESSION['username'].'

		';


		echo '<br><br>Your membership expires in: ';

		$login->membership_upto($_SESSION['username']);

		$token = bin2hex(random_bytes(64));

		echo '<br>Click <a href="add.php?account=',$_SESSION['username'],'&token=',$token,'&trial=true">here</a> to add one more month to it!';
	?>

</div>


<?php include('layout/_footer.php'); ?>