<?php
require("connect.php"); 

    $id = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT recipe.recipe_name, recipe.recipe_id, recipe.recipe_category, recipe.recipe_description, recipe.recipe_ingredients, recipe.recipe_instructions, user.user_displayName
     FROM recipe
     INNER JOIN user ON (recipe.user_id=user.user_id) 
     WHERE recipe_id = :recipe_id
     ORDER BY recipe.recipe_id DESC";
    
    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_id', $id, PDO::PARAM_INT);
    $statement->execute(); 
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<?php while ($row = $statement->fetch()): ?>  
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recipe: <?=$row['recipe_name']?></li>
         </ol>
    </nav>
        <div>       
            <h1><?=$row['recipe_name']?></h1>
            <button><?=$row['recipe_category']?></button>
            <p><?=$row['recipe_description']?></p>
            <p>By: <b><?=$row['user_displayName']?></b></p>
            <hr/>
            <h2>Ingredients</h2>
            <p><?=nl2br($row['recipe_ingredients'])?></p>
            <hr/>
            <h2>Instructions</h2> 
            <hr/>
            <p><?=nl2br($row['recipe_instructions'])?></p>         
        </div>
    <?php endwhile ?>

</body>
</html>