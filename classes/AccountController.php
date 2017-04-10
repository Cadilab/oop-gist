<?php

	class Login
	{
		// * Database variable

		private $db_connection = null;

		// * Logged status variable, setting it to false at the beggining

		private $user_is_logged_in = false;

		// * Errors and messages array

		public $errors = array();
		public $messages = array();

		// * Constructing the class

		public function __construct()
		{
			session_start();

			// * Checking if registration is all set

			if(isset($_POST['register']))
			{
				if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['email'])) {

					$this->registerNewUser($_POST['username'], $_POST['password'], $_POST['repassword'], $_POST['email']);
				}
				else
				{
					$this->errors[] = "You can't leave anything blank!";
				}
			}

			// * This is checking for login post

			elseif (isset($_POST['login']))
			{
				if(!empty($_POST['username']) && !empty($_POST['password']))
				{
					$this->loginUser($_POST['username'], $_POST['password']);
				}
				else
				{
					$this->errors[] = "You can't leave anything blank!";
				}
			}

			// * Check sessions

			elseif (isset($_SESSION['user_logged_in']))
			{
				$this->loginWithSessionData();
			}

			// * Check if everything is set for account upgrade

			elseif (!empty($_GET['account']) && !empty($_GET['token']) && !empty($_GET['trial']))
			{
				$this->addMonthToAccount($_GET['account']);
				exit();
			}
		}

		// * This is the function that connects us with the database

		public function databaseConnection()
		{
			if ($this->db_connection != null)
			{
				return true;
			}
			else
			{
				try 
				{
					$this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
					return true;
				}
				catch (PDOException $e) 
				{
                	$this->errors[] = "Database connection problem." . $e->getMessage();
            	}
			}
			return false;
		}

		// * This private function registers a new user

		private function registerNewUser($username, $password, $repassword, $email)
		{
        	$username = trim($username);

        	if ($this->databaseConnection())
        	{

	        	$check_data = $this->db_connection->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
	        	$check_data->bindValue(':username', $username, PDO::PARAM_STR);
	        	$check_data->bindValue(':email', $email, PDO::PARAM_STR);
	        	$check_data->execute();

	        	if($check_data->rowCount() > 0)
	        	{
	        		$this->errors[] = "Account with this credentials already exists";
	        	}
	        	else
	        	{
	        		if($password != $repassword)
	        		{
	        			$this->errors[] = "Passwords do not match!";
	        		}
	        		else
	        		{
	        			$password = password_hash($password, PASSWORD_BCRYPT);

						$cdate = new DateTime("+1 month");
						$expiry_date = $cdate->format('Y-m-d H:i:s');

		        		$insert_user = $this->db_connection->prepare("INSERT INTO users (username, password, email, regdate, expires) VALUES (:user, :password, :email, NOW(), :expires)");
		        		$insert_user->bindValue(':user', $username, PDO::PARAM_STR);
		        		$insert_user->bindValue(':password', $password, PDO::PARAM_STR);
		        		$insert_user->bindValue(':email', $email, PDO::PARAM_STR);
		        		$insert_user->bindValue(':expires', $expiry_date, PDO::PARAM_STR);
		        		$insert_user->execute();

		        		header("location: index.php");
		        		exit();
	        		}
	        	}
        	}
		}

		private function loginUser($username, $password)
		{
			$username = trim($username);

			if($this->databaseConnection())
			{
				$check_data = $this->db_connection->prepare("SELECT * FROM users WHERE username = :username");
				$check_data->bindValue(':username', $username, PDO::PARAM_STR);
				$check_data->execute();

				if($check_data->rowCount() > 0)
				{
					$row = $check_data->fetch(PDO::FETCH_ASSOC);

					if(password_verify($password, $row['password']))
					{
						if($this->valid_membership($username))
						{
							$_SESSION['user_logged_in'] = 1;
							$_SESSION['username'] = $row['username'];
							$_SESSION['userid'] = $row['id'];

							$this->user_is_logged_in = true;

							header("location: index.php");
							exit();
						}
						else
						{
							header("location: expiredmembership.php");
							exit();
						}
					}
					else
					{
						$this->errors[] = "Invalid password!";
					}
				}
				else
				{
					$this->errors[] = "Account with these credentials does not exist.";
				}
			}
		}

		// * This function is checking wether the user is logged in or not and then returning the logged status value

		public function loggedIn()
		{
			return $this->user_is_logged_in;
		}

		// * This funciton checks if the session is active on server and if it is it changes the default logged in value to true

		private function loginWithSessionData()
		{
			if(isset($_SESSION['user_logged_in']) && !empty($_SESSION['username']))
			{
				$this->user_is_logged_in = true;
			}
		}

		// * This is basic logout function

		public function doLogout()
		{
	        $_SESSION = array();
	        session_destroy();
	        $this->user_is_logged_in = false;

	        header("location: index.php");
	        exit();				
		}

		// * This function is checking if the membership is still active

		private function valid_membership($username)
		{
			if($this->databaseConnection())
			{
				$check_membership = $this->db_connection->prepare("SELECT * FROM users WHERE username = :username");
				$check_membership->bindValue(':username', $username, PDO::PARAM_STR);
				$check_membership->execute();

				if($check_membership->rowCount() > 0)
				{
					$row = $check_membership->fetch(PDO::FETCH_ASSOC);

					$cdate = new DateTime();
					$curent_date = $cdate->format('Y-m-d H:i:s');

					if($curent_date > $row['expires'])
					{
						return false;
					}
					else { return true; }
				}
			}
		}

		// * This function returns how many days left till membership expires

		public function membership_upto($username)
		{
			if($this->databaseConnection())
			{
				$valid_upto = "<b>Membership already expired!</b>";

				$check_expiration = $this->db_connection->prepare("SELECT * FROM users WHERE username = :username");
				$check_expiration->bindValue(':username', $username, PDO::PARAM_STR);
				$check_expiration->execute();

				if($check_expiration->rowCount() > 0)
				{
					$row = $check_expiration->fetch(PDO::FETCH_ASSOC);

					$date_expire = $row['expires'];
					$date1 = new DateTime('Now');
					$date2 = new DateTime($date_expire);
					$interval = $date1->diff($date2);

					if($date1 < $date2)
					{
						$valid_upto = $interval->format('%a days');
					}
					echo $valid_upto;
				}

				else { echo $valid_upto; }
			}
		}

		// * This will add a month worth of membership to users account

		public function addMonthToAccount($username)
		{
			if(!empty($_GET['account']) && !empty($_GET['token']) && !empty($_GET['trial']))
			{
				if($this->databaseConnection())
				{
					$check_month = $this->db_connection->prepare("SELECT * FROM users WHERE username = :username");
					$check_month->bindValue(':username', $username, PDO::PARAM_STR);
					$check_month->execute();

					if($check_month->rowCount() > 0)
					{
						$row = $check_month->fetch(PDO::FETCH_ASSOC);

						$date_expire = $row['expires'];
						$date = new DateTime($date_expire);
						$date->modify('+1 month');
						$expiry_date = $date->format('Y-m-d H:i:s');

						$add_month = $this->db_connection->prepare("UPDATE users SET expires = :expires WHERE username = :username");
						$add_month->bindValue(':expires', $expiry_date, PDO::PARAM_STR);
						$add_month->bindValue(':username', $username, PDO::PARAM_STR);
						$add_month->execute();

						header("location: index.php");
						exit();
					}
					else
					{
						header("location: index.php");
						exit();
					}
				}
			}
			else
			{
				header("location: index.php");
				exit();
			}
		}
	}

?>