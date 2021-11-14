<?php
$error = false;
session_start();
require('connect.php');

if($_POST && !empty($_POST['user_username']) && !empty($_POST['user_password'])) {
    $user_username = filter_input(INPUT_POST, 'user_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_password = filter_input(INPUT_POST, 'user_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $query2 = "SELECT user_id, user_username, user_password, user_displayName FROM user WHERE user_username = '$user_username'";
    $stmt = $db->prepare($query2); // Returns a PDOStatement object.
    $stmt->execute();
  
    foreach($stmt as $row) {
        $currentUserName = $row['user_username'];
        $currentPassword = $row['user_password'];
        $currentDisplayName = $row['user_displayName'];
        $currentUserId = $row['user_id'];    
    }

    if(!isset($currentUserName) && !isset($currentDisplayName)) {
        $currentUserName = null;
        $currentDisplayName = null;
    }

    if($_POST['user_username'] == $currentUserName && password_verify($_POST['user_password'], $currentPassword)) {
        if($currentUserName && $currentPassword) {
            $_SESSION['user_username'] = $user_username;
            $_SESSION['user_displayName'] = $currentDisplayName;
            $_SESSION['user_id'] = $currentUserId;
            header('location: index.php');
        } 
    }  else {
        $error = true;
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
    <title>Login</title>
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
          <a class="nav-link" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item dropdown">
        <?php if(isset($_SESSION['user_username'])):?>
          <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="create.php">Create Recipe</a>
          </li>
          <a class="nav-link active" aria-current="page" href="index.php?logout='1'">Sign Out<span class="navbar-text d-inline d-lg-none"> - <b><?php echo $_SESSION['user_displayName']?></b></span></a>
        <?php else:?>
          <a class="nav-link dropdown-toggle active" href="login.php" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sign In
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="login.php">Login</a></li>
            <li><a class="dropdown-item" href="register.php">Register</a></li>
          </ul>
        </li>
      </ul>
      <?php endif?>
    </div>
  </div>
  <?php if(isset($_SESSION['user_username'])):?>
      <span class="navbar-text ms-3 pe-4 d-none d-lg-block"><b><?php echo $_SESSION['user_displayName']?></b></span>
  <?php endif?>
</nav>
<!--BREADCRUMB NAV-->
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb ps-2 pt-1">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Login</li>
    </ol>
</nav>
<!--LOGON FORM-->
<div class="container">
        <form class="needs-validation p-4 border border-2" id="form" name="submit" action="login.php" method="POST" novalidate>          
            <label for="user_username" class="form-label">Username:</label> 
            <input type="text" class="form-control" id="user_username" name="user_username" aria-describedby="userUsernameHelp" required>
            <p class="invalid-feedback">Username cannot be empty.</p>
            <label for="user_password" class="form-label">Password:</label> 
            <input type="password" class="form-control" id="user_password" name="user_password" aria-describedby="userPasswordHelp" required>
            <p class="invalid-feedback">Password field cannot be empty.</p>
            <?php if($error):?>
                <div class="alert alert-danger mt-1" role="alert">
                    Cannot find user with that username or password.
                </div>
            <?php endif?>
            <button class="btn btn-dark mt-2">Login</button>
        </form>
    </div>
<script src="validator.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>