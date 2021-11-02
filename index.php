<?php
    require("connect.php"); 
   
    $query = "SELECT recipe.recipe_name, recipe.recipe_id, recipe.recipe_category, recipe.recipe_description, user.user_displayName
     FROM recipe
     INNER JOIN user ON (recipe.user_id=user.user_id) 
     ORDER BY recipe.recipe_id DESC";
    
      //$query = "SELECT * FROM recipe";
      $statement = $db->prepare($query); // Returns a PDOStatement object.
      $statement->execute(); // The query is now executed.
   
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
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Home</a></li>
        </ol>
    <?php while ($row = $statement->fetch()): ?>  
        <div class="card" style="width: 18rem;">
            <img src="..." class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title"><a href="recipe.php?recipe_id=<?=$row['recipe_id']?>"><?= $row['recipe_name'] ?></a></h5>
                <button><?=$row['recipe_category']?></button>
                <p class="card-text"><?=$row['recipe_description']?></p>
                <p>By: <?=$row['user_displayName']?></p>
             </div>
        </div>
    <?php endwhile ?>
    <?php if($statement->rowCount() <= 0):?>
        <p>Sorry, there are no current recipes yet!</p>
    <?php endif?>
</body>
</html>