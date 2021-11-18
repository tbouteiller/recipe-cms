<?php
  session_start();
  require("connect.php"); 

  //DELETE COMMENT
  if($_POST && isset($_POST['comment_id'])){
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
    $query4 = "DELETE FROM comment WHERE comment_id = :comment_id LIMIT 1";
    $statement4 = $db->prepare($query4);
    $statement4->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
    $statement4->execute();
   }

  //RECIPE SELECT
  if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
  } else $user_id = null;

  $id = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
  $query = "SELECT recipe.recipe_name, recipe.recipe_id, recipe.recipe_category, recipe.recipe_description, recipe.recipe_ingredients, recipe.recipe_instructions, user.user_displayName
    FROM recipe
    INNER JOIN user ON (recipe.user_id=user.user_id) 
    WHERE recipe_id = :recipe_id
    ORDER BY recipe.recipe_id DESC";
    
  $statement = $db->prepare($query);
  $statement->bindValue(':recipe_id', $id, PDO::PARAM_INT);
  $statement->execute();

  //COMMENT INSERT
  if($_POST && !empty($_POST['comment_content'])) {
    $id = filter_input(INPUT_GET, 'recipe_id', FILTER_SANITIZE_NUMBER_INT);
    $user_id = $_SESSION['user_id'];
    $comment_content  = filter_input(INPUT_POST, 'comment_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $query2= "INSERT INTO comment (user_id, recipe_id, comment_content)
    VALUES (:user_id, :recipe_id, :comment_content)";
    $statement2 = $db->prepare($query2);
    $statement2->bindValue(':user_id', $user_id);
    $statement2->bindValue(':recipe_id', $id, PDO::PARAM_INT);
    $statement2->bindValue(':comment_content', $comment_content);
    $statement2->execute();
    }

    //COMMENT SELECT
    $query3 = "SELECT comment.comment_content, comment.comment_id, user.user_displayName, user.user_username
    FROM comment
    INNER JOIN user ON (comment.user_id=user.user_id) 
    WHERE recipe_id = :recipe_id
    ORDER BY comment.comment_id DESC LIMIT 10";
    $statement3 = $db->prepare($query3);
    $statement3->bindValue(':recipe_id', $id);
    $statement3->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Recipe</title>
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
<!--BREADCRUMB WITH CONTENT-->
<?php while ($row = $statement->fetch()): ?>  
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb ps-2 pt-1">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recipe: <?=$row['recipe_name']?></li>
         </ol>
    </nav>
    <div class="mb-8 container">
      <div class="bg-light p-4 rounded-3 border border-2 mb-1">
            <h3><strong><?=$row['recipe_name']?></strong></h3>
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
            <p><?=$row['recipe_description']?></p>
            <p>By: <b><?=$row['user_displayName']?></b></p>
      </div>      
            <h4>Ingredients</h4>
            <p><?=nl2br($row['recipe_ingredients'])?></p>      
            <h4>Instructions</h4>       
            <p><?=nl2br($row['recipe_instructions'])?></p>   
            <hr/>      
        </div>
    <?php endwhile ?>
    <!--COMMENT-->
    <div class="container">
      <h3>Comments (<?=$statement3->rowCount()?>)</h3>
      <?php if(isset($_SESSION['user_username'])):?>
      <form class="needs-validation" id="form" action='recipe.php?recipe_id=<?=$id?>' name="comment" method="POST" novalidate>
        <label for="comment_content" class="form-label">Comment:</label> 
        <input type="text" class="form-control" id="comment_content" name="comment_content" aria-describedby="commentContentHelp" required>
        <button class="btn btn-primary btn-sm my-2" type="submit">Add</button>
      </form>
      <?php endif?>
      <?php while($test = $statement3->fetch()):?>
        <div class="card text-dark bg-light mb-3 rounded-3 mt-1" style="max-width: 18rem;">
          <div class="card-header p-2"><strong><?=$test['user_displayName']?></strong></div>
            <div class="card-body p-2">
              <p class="card-text m-0"><?=$test['comment_content']?></p>
              <?php if(isset($_SESSION['user_username']) && $_SESSION['user_username'] === $test['user_username'] || isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'admin'):?>
              <form action='recipe.php?recipe_id=<?=$id?>' name="deleteData" type="submit" method="POST">
                <input type="hidden" name="comment_id" value="<?=$test['comment_id']?>">
                <button class="btn btn-outline-danger btn-sm mt-1 py-0 px-1">Delete</button>
              </form>
              <?php endif?>
            </div>
        </div>
      <?php endwhile?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="validator.js"></script>  
</body>
</html>