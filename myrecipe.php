<?php
    session_start();
    require('connect.php');
    require('session.php');

    $id = $_SESSION['user_id'];
    $query = "SELECT recipe.recipe_name, recipe.user_id, recipe.recipe_id, recipe.recipe_category, recipe.recipe_description, recipe.recipe_ingredients, recipe.recipe_instructions, user.user_displayName
     FROM recipe
     INNER JOIN user ON (recipe.user_id=user.user_id)
     WHERE recipe.user_id = :user_id
     ORDER BY recipe.recipe_id DESC";   
    $statement = $db->prepare($query); // Returns a PDOStatement object.
    $statement->bindValue(':user_id', $id, PDO::PARAM_INT);
    $statement->execute(); // The query is now executed.

    //ADMIN RECIPE SELECT
    if($_SESSION['user_username'] === 'admin')
    {
      $admin = $_SESSION['user_username'];
      $adminQuery = "SELECT * FROM recipe";
      $adminStmt = $db->prepare($adminQuery);
      $adminStmt->execute();

      $adminUserQuery = $_SESSION['user_username'];
      $adminUserQuery = "SELECT * FROM user";
      $adminUserStmt = $db->prepare($adminUserQuery);
      $adminUserStmt->execute();
    }

    //ADMIN UPDATE
    IF($_POST && isset($_POST['adminUpdate'])) {
      $username  = filter_input(INPUT_POST, 'adminUsername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $displayName  = filter_input(INPUT_POST, 'adminDisplayName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $userId = filter_input(INPUT_POST, 'adminUserId', FILTER_SANITIZE_NUMBER_INT);
      $adminUpdateQuery = "UPDATE user SET user_displayName = :user_displayName, user_username = :user_username WHERE user_id = :user_id";
      $adminUpdateStmt = $db->prepare($adminUpdateQuery);
      $adminUpdateStmt->bindValue(':user_displayName', $displayName);
      $adminUpdateStmt->bindValue(':user_username', $username);
      $adminUpdateStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
      $adminUpdateStmt->execute();
      header("Location: myrecipe.php");
      exit;
    } 
    //ADMIN DELETE
    IF($_POST && isset($_POST['adminDelete'])) {
      $userId = filter_input(INPUT_POST, 'adminUserId', FILTER_SANITIZE_NUMBER_INT);
      $adminDeleteQuery = "DELETE FROM user WHERE user_id = :user_id LIMIT 1";
      $adminDeleteStmt = $db->prepare($adminDeleteQuery);
      $adminDeleteStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
      $adminDeleteStmt->execute();
      header("Location: myrecipe.php");
      exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>My Recipes</title>
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
          <a class="nav-link" aria-current="page" href="create.php">Create Recipe</a>
          </li>
          <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="myrecipe.php">My Recipes</a>
          </li>
          <li><a class="nav-link" aria-current="page" href="index.php?logout='1'">Sign Out<span class="navbar-text d-inline d-lg-none"> - <b><?php echo $_SESSION['user_displayName']?></b></span></a></li>
        <?php else:?>
          <a class="nav-link dropdown-toggle" href="login.php" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sign In
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="login.php">Login</a></li>
            <li><a class="dropdown-item" href="register.php">Register</a></li>
          </ul>
        </li>
      <?php endif?>
      </ul>
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
        <li class="breadcrumb-item active" aria-current="page">My Recipes</li>
    </ol>
</nav>
<!--USER RECIPES-->
<div class="mb-8 container">
<?php while ($row = $statement->fetch()): ?>  
        <div class="mb-8">
          <div class="bg-light p-4 rounded-3 border border-2 mb-1">     
            <h3><strong><?=$row['recipe_name']?></strong></h3>
            <p class="
                  <?php if($row['recipe_category'] == 'Lunch'):?>
                  btn-warning
                  <?php elseif($row['recipe_category'] == 'Breakfast'):?>
                  btn-success
                  <?php elseif($row['recipe_category'] == 'Dessert'):?>
                  btn-info
                  <?php elseif($row['recipe_category'] == 'Dinner'):?>
                  btn-danger
                  <?php endif?>
                  btn disabled btn-sm rounded-pill px-3"><?=$row['recipe_category']?></p>   
            <p class="mb-0"><?=$row['recipe_description']?></p>
            <p class="mt-0">By: <b><?=$row['user_displayName']?></b></p>
          </div>       
            <h4>Ingredients</h4>
            <p><?=nl2br($row['recipe_ingredients'])?></p>      
            <h4>Instructions</h4>       
            <p><?=nl2br($row['recipe_instructions'])?></p>
            <a class="btn btn-primary btn-sm mb-1" href="edit.php?recipe_id=<?=$row['recipe_id']?>">Edit</a>    
        </div>
<hr/>
    <?php endwhile ?>
</div>
<!--ADMIN RECIPES-->
<?php if(isset($admin)):?>
  <div class="container">
    <h4>All Recipes</h4>
    <table class="table">
    <thead>
    <tr>
      <th scope="col">Id#</th>
      <th scope="col">Title</th>
      <th scope="col">Edit</th>
    </tr>
  </thead>
    <?php while($row = $adminStmt->fetch()):?>
      <tbody>
        <th scope="row"><?=$row['recipe_id']?></th>
        <td><?=$row['recipe_name']?></td>
        <td><a class="btn btn-primary btn-sm mb-1" href="edit.php?recipe_id=<?=$row['recipe_id']?>">Edit</a></td>
      </tbody>
    <?php endwhile?>
    </table>

    <h4>All Users</h4>
    <table class="table">
    <thead>
    <tr>
      <th scope="col">User Id#</th>
      <th scope="col">Display Name</th>
      <th scope="col">Username</th>
      <th scope="col">Update</th>
      <th scope="col">Delete</th>
    </tr>
  </thead>
    <?php while($row = $adminUserStmt->fetch()):?>
      <tbody>
        <th scope="row"><?=$row['user_id']?></th>
        <form action="myrecipe.php" method="post">
        <input hidden name="adminUserId" value="<?=$row['user_id']?>">
        <td><input name="adminDisplayName" value="<?=$row['user_displayName']?>"></td>
        <td><input name="adminUsername" value="<?=$row['user_username']?>"></td>
        <td><button name="adminUpdate" class="btn btn-primary btn-sm" type="submit">Update</button></td>
        <td><button name="adminDelete" class="btn btn-danger btn-sm"  type="submit">Delete</button></td>
      </form>
      </tbody>
    <?php endwhile?>
    </table>
  </div>
<?php endif?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>