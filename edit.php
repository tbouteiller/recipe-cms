<?php
session_start();
require('connect.php');
require('session.php');
$id = $_SESSION['user_id'];

//USER GET
if(isset($_GET['recipe_id']) && $_SESSION['user_username'] != 'admin') {
        
    $recid = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM recipe WHERE user_id = '$id' AND recipe_id = :recipe_id ORDER BY recipe.recipe_id DESC";
    $statement = $db->prepare($query);
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
    $id = $_SESSION['user_id'];
    $recid = filter_input(INPUT_POST, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "DELETE FROM recipe WHERE recipe_id = :recipe_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_id', $recid, PDO::PARAM_INT);
    $statement->execute();
    header("Location: myrecipe.php");
    exit;
}

//UPDATE
if($_POST && isset($_POST['recipe_name'])) {

    $id = $_SESSION['user_id'];
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
        $statement->bindValue(':user_id', $id);
        $statement->bindValue(':recipe_id', $recid);
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
        $statement->bindValue(':recipe_id', $recid);
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
    <title>Edit Recipe</title>
</head>
<body>
<!--CREATE FORM-->
<div class="container">
        <form class="needs-validation" id="form" name="submit" action="edit.php" method="POST" novalidate>
            <input type="hidden" name="recipe_id" value="<?=$fields['recipe_id']?>">
            <label for="recipe_name" class="form-label">Recipe Name:</label> 
            <input value="<?=$fields['recipe_name']?>"type="text" class="form-control" id="recipe_name" name="recipe_name" aria-describedby="recipeNameHelp" required>
            <p class="invalid-feedback">You need a name for your recipe.</p>
            <label for="recipe_description" class="form-label">Recipe Description:</label> 
            <textarea class="form-control" id="recipe_description" name="recipe_description" placeholder="Enter the recipes description here..."aria-describedby="recipeDescriptionHelp" required><?=$fields['recipe_description']?></textarea>
            <p class="invalid-feedback">Provide a description for your recipe.</p>
            <label for="form-select" class="form-label">Recipe Category:</label> 
            <select name ="recipe_category" class="form-select" aria-label="recipe category" required>
            <option selected disabled value="">Select a Category:</option>
                <option value="1|Breakfast">Breakfast</option>
                <option value="2|Lunch">Lunch</option>
                <option value="3|Dinner">Dinner</option>
                <option value="4|Dessert">Dessert</option>
            </select>
            <label for="recipe_ingredients" class="form-label">Ingredients:</label> 
            <textarea class="form-control" id="recipe_ingredients" name="recipe_ingredients" placeholder="Enter your ingredients here..."aria-describedby="recipeIngredientsHelp"><?=$fields['recipe_ingredients']?></textarea>
            <p class="invalid-feedback">Enter the ingredients you used to make this recipe.</p>
            <label for="recipe_instructions" class="form-label">Instructions:</label> 
            <textarea class="form-control" id="recipe_instructions" name="recipe_instructions" placeholder="Step 1..."aria-describedby="recipeInstructionsHelp"><?=$fields['recipe_instructions']?></textarea>
            <p class="invalid-feedback">Provide instructions on how you made this recipe.</p>
           <!-- <label for="recipe_picture" class="form-label">Recipe Picture:</label> 
            <input type="file" class="form-control" id="recipe_picture" name="recipe_picture" aria-describedby="recipePictureeHelp">-->
            <div>
                <button class="update" type="submit">Update</button>
                <button class="delete" type="submit" name="deleteData" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
            </div>
        </form>
    </div>
</body>
</html>