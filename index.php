<?php
session_start();
    require("connect.php"); 
   
    $query = "SELECT recipe.recipe_name, recipe.recipe_id, recipe.recipe_category, recipe.recipe_description, user.user_displayName
     FROM recipe
     INNER JOIN user ON (recipe.user_id=user.user_id) 
     ORDER BY recipe.recipe_id DESC"; 
    $statement = $db->prepare($query); // Returns a PDOStatement object.
    $statement->execute(); // The query is now executed.

    if (isset($_GET['logout'])) {
      session_destroy();
      unset($_SESSION['user_username']);
      unset($_SESSION['user_displayName']);
      header("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Right Recipe | Home</title>
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
          <a class="nav-link active" aria-current="page" href="">Home</a>
        </li>
        <li class="nav-item dropdown">
        <?php if(isset($_SESSION['user_username'])):?>
          <li class="nav-item">
          <a class="nav-link" aria-current="page" href="create.php">Create Recipe</a>
          </li>
          <li class="nav-item">
          <a class="nav-link" aria-current="page" href="myrecipe.php">My Recipes</a>
          </li>
          <a class="nav-link" aria-current="page" href="index.php?logout='1'">Sign Out<span class="navbar-text d-inline d-lg-none"> - <b><?php echo $_SESSION['user_displayName']?></b></span></a>
        <?php else:?>
          <a class="nav-link dropdown-toggle" href="login.php" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
<!--BANNER IMG-->
<img class="w-100 shadow" src="banner2.jpeg" alt="banner">
<!--BREADCRUMB NAV-->
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
  <ol class="breadcrumb ps-2 pt-1">
    <li class="breadcrumb-item active" aria-current="page">Home</a></li>
  </ol>
</nav>
<!--MAIN CONTENT-->
  <div class="container-fluid w-75">
    <div class="row justify-content-center">
    <?php while ($row = $statement->fetch()): ?>
        <div class="card m-2 shadow-sm" style="width: 18rem;">
            <!--<img src="..." class="card-img-top" alt="...">-->
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h5 class="card-title"><a class="text-decoration-none link-dark" href="recipe.php?recipe_id=<?=$row['recipe_id']?>"><strong><?= $row['recipe_name'] ?></strong></a></h5>
                <p class="
                  <?php if($row['recipe_category'] === 'Lunch'):?>
                  btn-warning
                  <?php elseif($row['recipe_category'] === 'Breakfast'):?>
                  btn-success
                  <?php elseif($row['recipe_category'] === 'Dessert'):?>
                  btn-info
                  <?php elseif($row['recipe_category'] === 'Dinner'):?>
                  btn-danger
                  <?php endif?>
                  btn disabled btn-sm rounded-pill px-3"><?=$row['recipe_category']?></p>       
                <p class="card-text"><?=$row['recipe_description']?></p>
              </div>
                <div><p class="mt-1">By: <?=$row['user_displayName']?></p></div>
             </div>
        </div>
    <?php endwhile ?>
    </div>
    <?php if($statement->rowCount() <= 0):?>
        <p>Sorry, there are no current recipes yet!</p>
    <?php endif?>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>