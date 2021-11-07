<?php
require('connect.php');
if($_POST && !empty($_POST['recipe_name']) && !empty($_POST['recipe_description'])) {

    $recipe_name  = filter_input(INPUT_POST, 'recipe_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $recipe_description = filter_input(INPUT_POST, 'recipe_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $categorySplit = explode('|', $_POST['recipe_category']);
    $recipe_category = $categorySplit[1];
    $recipe_ingredients = filter_input(INPUT_POST, 'recipe_ingredients', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $recipe_instructions = filter_input(INPUT_POST, 'recipe_instructions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = 1;

    $query= "INSERT INTO recipe (recipe_name, user_id, recipe_description, recipe_category, recipe_ingredients, recipe_instructions)
    VALUES (:recipe_name, :user_id, :recipe_description, :recipe_category, :recipe_ingredients, :recipe_instructions)";
    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_name', $recipe_name);
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':recipe_description', $recipe_description);
    $statement->bindValue(':recipe_category', $recipe_category);
    $statement->bindValue(':recipe_ingredients', $recipe_ingredients);
    $statement->bindValue(':recipe_instructions', $recipe_instructions);
    //$statement->bindValue(':recipe_image', $filename);
    $statement->execute();
    //$insert_id = $db->lastInsertId();
    header("Location: index.php");
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
    <title>Create A Recipe</title>
</head>
<body>
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Recipe</li>
         </ol>
    </nav>
    <div class="container">
        <form class="needs-validation" id="form" name="submit" action="create.php" method="POST" novalidate>
            <label for="recipe_name" class="form-label">Recipe Name:</label> 
            <input type="text" class="form-control" id="recipe_name" name="recipe_name" aria-describedby="recipeNameHelp" required>
            <p class="invalid-feedback">You need a name for your recipe.</p>
            <label for="recipe_description" class="form-label">Recipe Description:</label> 
            <textarea class="form-control" id="recipe_description" name="recipe_description" placeholder="Enter the recipes description here..."aria-describedby="recipeDescriptionHelp" required></textarea>
            <p class="invalid-feedback">Provide a description for your recipe.</p>
            <label for="form-select" class="form-label">Recipe Category:</label> 
            <select name ="recipe_category" class="form-select" aria-label="recipe category" required>
            <option selected disabled value="">Select a Category:</option>
                <option value="1|Chicken">Chicken</option>
                <option value="2|Breakfast">Breakfast</option>
                <option value="3|Pasta">Pasta</option>
                <option value="4|Fish">Fish</option>
            </select>
            <label for="recipe_ingredients" class="form-label">Ingredients:</label> 
            <textarea class="form-control" id="recipe_ingredients" name="recipe_ingredients" placeholder="Enter your ingredients here..."aria-describedby="recipeIngredientsHelp"></textarea>
            <p class="invalid-feedback">Enter the ingredients you used to make this recipe.</p>
            <label for="recipe_instructions" class="form-label">Instructions:</label> 
            <textarea class="form-control" id="recipe_instructions" name="recipe_instructions" placeholder="Step 1..."aria-describedby="recipeInstructionsHelp"></textarea>
            <p class="invalid-feedback">Provide instructions on how you made this recipe.</p>
           <!-- <label for="recipe_picture" class="form-label">Recipe Picture:</label> 
            <input type="file" class="form-control" id="recipe_picture" name="recipe_picture" aria-describedby="recipePictureeHelp">-->
            <button>Submit</button>
        </form>
    </div>
<script>
    (function () {
    'use strict'

    var forms = document.querySelectorAll('.needs-validation')

    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>
</body>
</html>