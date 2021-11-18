<?php
session_start();
require('connect.php');
require('session.php');
$id = $_SESSION['user_id'];

//USER GET
if(isset($_GET['recipe_id']) && $_SESSION['user_username'] != 'admin') {
        
    $recid = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM recipe WHERE user_id = :user_id AND recipe_id = :recipe_id ORDER BY recipe.recipe_id DESC";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $id, PDO::PARAM_INT);
    $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
    $statement->execute();
    $fields = $statement->fetch();
} 

//ADMIN GET
if(isset($_GET['recipe_id']) && (isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'admin')) {
        
    $recid = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM recipe WHERE recipe_id = :recipe_id ORDER BY recipe.recipe_id DESC";
    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
    $statement->execute();
    $fields = $statement->fetch();
} 

//DELETE
if($_POST && isset($_POST['deleteData']) ){
    $recid = filter_input(INPUT_POST, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "DELETE FROM recipe WHERE recipe_id = :recipe_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
    $statement->execute();
    header("Location: myrecipe.php");
    exit;
}

//UPDATE FUNCTIONALITY
if($_POST && !empty($_POST['recipe_name']) && !empty($_POST['recipe_description']) && !empty($_POST['recipe_category']) && !empty($_POST['recipe_ingredients']) && !empty($_POST['recipe_instructions'])) {
    $recipe_name  = filter_input(INPUT_POST, 'recipe_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $recid = filter_input(INPUT_POST, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $recipe_description = filter_input(INPUT_POST, 'recipe_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $categorySplit = explode('|', $_POST['recipe_category']);
    $recipe_category = $categorySplit[1];
    $recipe_ingredients = filter_input(INPUT_POST, 'recipe_ingredients', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $recipe_instructions = filter_input(INPUT_POST, 'recipe_instructions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //UDPATES FOR REGULAR USER
    if($_SESSION['user_username'] != 'admin') {
        $query2 = "UPDATE recipe SET recipe_name = :recipe_name, user_id = :user_id, recipe_description = :recipe_description, 
         recipe_category = :recipe_category, recipe_ingredients = :recipe_ingredients, 
         recipe_instructions = :recipe_instructions WHERE recipe_id = :recipe_id";     
        $statement = $db->prepare($query2);
        $statement->bindValue(':recipe_name', $recipe_name);
        $statement->bindValue(':user_id', $id, PDO::PARAM_INT);
        $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
        $statement->bindValue(':recipe_description', $recipe_description);
        $statement->bindValue(':recipe_category', $recipe_category);
        $statement->bindValue(':recipe_ingredients', $recipe_ingredients);
        $statement->bindValue(':recipe_instructions', $recipe_instructions);
        $statement->execute();
        header("Location: myrecipe.php");
        exit;
        
    }
    
    //UPDATE FOR ADMIN
    if($_SESSION['user_username'] === 'admin') {
        $query2 = "UPDATE recipe SET recipe_name = :recipe_name, recipe_description = :recipe_description, 
         recipe_category = :recipe_category, recipe_ingredients = :recipe_ingredients, 
         recipe_instructions = :recipe_instructions WHERE recipe_id = :recipe_id";
        $statement = $db->prepare($query2);
        $statement->bindValue(':recipe_name', $recipe_name);
        $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
        $statement->bindValue(':recipe_description', $recipe_description);
        $statement->bindValue(':recipe_category', $recipe_category);
        $statement->bindValue(':recipe_ingredients', $recipe_ingredients);
        $statement->bindValue(':recipe_instructions', $recipe_instructions);
        $statement->execute();
        header("Location: myrecipe.php");
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
    <title>Edit Recipe</title>
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
          <a class="nav-link" aria-current="page" href="myrecipe.php">My Recipes</a>
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
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>
<!--CREATE FORM-->
<div class="container">
    <form class="needs-validation" id="form" name="submit" action="edit.php" method="POST" novalidate>
        <input type="hidden" name="recipe_id" value="<?=$fields['recipe_id']?>">
        <label for="recipe_name" class="form-label">Recipe Name:</label> 
        <input value="<?=$fields['recipe_name']?>" type="text" class="form-control" id="recipe_name" name="recipe_name" required>
        <p class="invalid-feedback">You need a name for your recipe.</p>
        <label for="recipe_description" class="form-label">Recipe Description:</label> 
        <textarea class="form-control" id="recipe_description" name="recipe_description" placeholder="Enter the recipes description here..." required><?=$fields['recipe_description']?></textarea>
        <p class="invalid-feedback">Provide a description for your recipe.</p>
        <label for="recipe_category" class="form-label">Recipe Category:</label> 
        <select id="recipe_category" name ="recipe_category" class="form-select" aria-label="recipe category" required>
            <option disabled value="">Select a Category:</option>
            <option value="1|Breakfast" <?php if($fields['recipe_category'] == 'Breakfast'):?><?='selected="selected"'?><?php endif?> >Breakfast</option>
            <option value="2|Lunch" <?php if($fields['recipe_category'] == 'Lunch'):?><?='selected="selected"'?><?php endif?>>Lunch</option>
            <option value="3|Dinner" <?php if($fields['recipe_category'] == 'Dinner'):?><?='selected="selected"'?><?php endif?>>Dinner</option>
            <option value="4|Dessert" <?php if($fields['recipe_category'] == 'Dessert'):?><?='selected="selected"'?><?php endif?>>Dessert</option>
        </select>
        <label for="recipe_ingredients" class="form-label">Ingredients:</label> 
        <textarea class="form-control" id="recipe_ingredients" name="recipe_ingredients" placeholder="Enter your ingredients here..."><?=$fields['recipe_ingredients']?></textarea>
        <p class="invalid-feedback">Enter the ingredients you used to make this recipe.</p>
        <label for="recipe_instructions" class="form-label">Instructions:</label> 
        <textarea class="form-control" id="recipe_instructions" name="recipe_instructions" placeholder="Step 1..."><?=$fields['recipe_instructions']?></textarea>
        <p class="invalid-feedback">Provide instructions on how you made this recipe.</p>
        <div class="mt-2">
            <button class="update btn btn-primary btn-sm" type="submit">Update</button>
            <button class="delete btn btn-danger btn-sm" type="submit" name="deleteData" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
        </div>
        </form>
    </div>
<script src="validator.js"></script>  
</body>
</html>