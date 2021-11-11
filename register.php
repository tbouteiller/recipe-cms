<?php
session_start();
$userNameTaken = false;
$displayNameTaken = false;

require('connect.php');
if($_POST && !empty($_POST['user_displayName']) && !empty($_POST['user_username']) && !empty($_POST['user_password'])  && !empty($_POST['user_password_verify']) && $_POST['user_password'] == $_POST['user_password_verify']) {

    $user_displayName  = filter_input(INPUT_POST, 'user_displayName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_username = filter_input(INPUT_POST, 'user_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_password = filter_input(INPUT_POST, 'user_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_password_verify = filter_input(INPUT_POST, 'user_password_verify', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //first check if the user already exists
    $query2 = "SELECT user_username, user_displayName FROM user WHERE user_username = '$user_username' OR user_displayName = '$user_displayName'";
    $stmt = $db->prepare($query2); // Returns a PDOStatement object.
    $stmt->execute();

    foreach($stmt as $row) {
      $usernameInDatabase = $row['user_username'];
      $userDisplayNameInDatabase = $row['user_displayName'];
    }

    if($usernameInDatabase === $user_username) {
      $userNameTaken = true;
     
    }

    if($userDisplayNameInDatabase === $user_displayName) {
      $displayNameTaken = true;
    
    } 

    //add to db if not taken
    if((isset($usernameInDatabase) != $user_username) && (isset($userDisplayNameInDatabase) != $user_displayName)) {
    $query= "INSERT INTO user (user_displayName, user_username, user_password)
    VALUES (:user_displayName, :user_username, :user_password)";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_displayName', $user_displayName);
    $statement->bindValue(':user_username', $user_username);
    $statement->bindValue(':user_password', $user_password);
    $statement->execute();
    $_SESSION['username'] = $user_username;
  	$_SESSION['success'] = "You are now logged in";
    header("Location: index.php");
    exit;
    } 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Register</title>
</head>
<body>
  <!--MAIN NAV BAR-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Right Recipe</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sign In
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="#">Login</a></li>
            <li><a class="dropdown-item" href="register.php">Register</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
  <!--BREADCRUMB NAV-->
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb ps-2 pt-1">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="Register">Register</li>
         </ol>
    </nav>
    <!--REGISTER FORM-->
    <div class="container">
        <form class="needs-validation" id="form" name="submit" action="register.php" method="POST" novalidate oninput='user_password_verify.setCustomValidity(user_password_verify.value != user_password.value ? "Passwords do not match." : "")'>          
            <label for="user_displayName" class="form-label">User Display Name:</label> 
            <input type="text" class="form-control" id="user_displayName" name="user_displayName" aria-describedby="userDisplayName" required>
            <p class="invalid-feedback">Please enter your display name.</p>
            <?php while($displayNameTaken):?>
              <p class="text-danger fs-6">Sorry, that display name is taken!</p>
              <?=($displayNameTaken = false)?>
            <?php endwhile?>
            <label for="user_username" class="form-label">Please enter a username:</label> 
            <input type="text" class="form-control" id="user_username" name="user_username" aria-describedby="userUsernameHelp" required>
            <p class="invalid-feedback">Please enter a username.</p>
            <?php while($userNameTaken):?>
              <p class="text-danger fs-6">Sorry, that username is taken!</p>
              <?=($userNameTaken = false)?>
            <?php endwhile?>
            <label for="user_password" class="form-label">Please enter a password:</label> 
            <input type="password" class="form-control" id="user_password" name="user_password" aria-describedby="userPasswordHelp" required>
            <p class="invalid-feedback">Please enter a password.</p>
            <label for="user_password_verify" class="form-label">Please verify your password:</label> 
            <input type="password" class="form-control" id="user_password_verify" name="user_password_verify" aria-describedby="userPasswordVerifyHelp" required>
            <p class="invalid-feedback">Please ensure your passwords match.</p>
            <button>Register</button>
        </form>
    </div>
<script src="validator.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>