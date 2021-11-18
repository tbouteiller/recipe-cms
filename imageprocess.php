<?php
//IMAGE UPLOAD
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["recipe_image"]["name"]);
$uploadOk = 1;
$error = null;
$success = null;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(is_uploaded_file($_FILES['recipe_image']['tmp_name'])) {
  // Check if file already exists
  if (file_exists($target_file)) {
    $uploadOk = 0;
    $error = "File already exists.";
  }

  // Check file size
  if ($_FILES["recipe_image"]["size"] > 500000) {
    $uploadOk = 0;
    $error = "File size too large.";
  }

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    $uploadOk = 0;
    $error = "Image isn't a supported file format.";
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    $target_file = null;
  // if everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
      $success = "The file ". htmlspecialchars( basename( $_FILES["recipe_image"]["name"])). " has been uploaded.";
      } else {
        $error = "Sorry, there was an error uploading your file.";       
      }
  }
} else {
  $target_file = null;
}
?>