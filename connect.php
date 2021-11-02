<?php
    $dsn = 'mysql:dbname=recipe_cms;host=localhost';
    $user = 'recipe_cms_user';
    $password = 'rOQiK_RlQE@_Gs9F';
    
    try 
    {
        $db = new PDO($dsn, $user, $password);
    } 
    
    catch (PDOException $e) 
    {
        echo 'Connection failed: ' . $e->getMessage();
        exit;   
    }
    
    ?>
