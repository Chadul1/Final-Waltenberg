<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
       <!-- Bootstrap CSS -->
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
       <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymces.min.js" referrerpolicy="origin"></script>
</head>
    <body>
    <?php require('partials/navbar.php');?>
        <main class="container my-2">
            <h1 class="m-2">Most Recent Posts</h1>
            <?php if(isset($_SESSION['user'])) : ?>
                <?php if($_SESSION['user']['IsBanned'] === 1) : ?>
                    <h1>Sorry, you have been banned.</h1>
                <?php else : ?>
                    <?php require('partials/userPost.php');?>
                <?php endif; ?>
            <?php else : ?>
                <?php require('partials/userPost.php');?>
            <?php endif ; ?>
        </main>
    </body>
</html>
<?php 
    if(isset($_SESSION['Posts'])){
        $_SESSION['Posts'] = null;
    }
?> 