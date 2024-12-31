<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
        <?php  
            if(isset($_SESSION['SelectedPost'])) {$post = $_SESSION['SelectedPost'];}
            if(isset( $_SESSION['UserProfile'])){$user = $_SESSION['UserProfile']; }
            if (isset($_SESSION['errors'])) {
                $errors = $_SESSION['errors'];
                // Clear the errors after displaying them
                unset($_SESSION['errors']);
            }
        ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
       <script src="https://cdn.tiny.cloud/1/kgna46wvl0hnwbebyx7jdt7fxgw09zw82vajtvhqp18l20uz/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
        <script  src="../../JS/tinymce/js/tinymce/tinymce.min.js"></script>
        <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
        <script>
            tinymce.init({
                selector: "#tiny",
                plugins: 'a_tinymce_plugin',
                a_plugin_option: true,
                a_configuration_option: 400,
                toolbar_mode: 'floating',
                height: 500
            });
        </script>
</head>
    <body>
    <?php require('partials/navbar.php');?>
        <main class="container my-2" >
        <?php use function app\functions\slugify; ?>
            <?php if(isset($_SESSION['SelectedPost'][0])): ?>
                <div class="container rounded-top border border-0-bottom bg-white mb-3">
                    <!-- Forum Post -->
                    <div class="p-3">
                    <!-- Header -->
                        <div class="row align-items-center mb-2">
                            <h4 class="col text-start mb-0"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                href="/Final-Project-Waltenberg/public/posts/<?= base64_encode($post[0]->PostID)?>/<?= urlencode(slugify($post[0]->Title))?>/">
                                <?= htmlspecialchars_decode($post[0]->Title) ?></a></h4>
                            <div class="col-auto ms-auto">
                                <small class="text-muted">Posted on: <?= explode(" ",$post[0]->PublishDate)[0]?></small>
                            </div>
                            <div class="col-auto ms-auto">
                                <?php if(isset($_SESSION['user']['UserRole'])) : ?>
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle border" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style=" background-color: rgb(254, 250, 224);"></button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/posts/flag/<?= base64_encode($post[0]->PostID)?>/<?= urlencode(slugify($post[0]->Title))?>/">Report</a></li>
                                            <?php if($_SESSION['user']['UserRole'] === 'Admin') : ?>
                                                <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/posts/archive/<?= base64_encode($post[0]->PostID)?>/<?= urlencode(slugify($post[0]->Title))?>/">Archive</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted">by <a class="link-dark  link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                            href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($post[0]->UserID))?>/<?= urlencode($post[0]->Username)?>/">
                            <?= htmlspecialchars($post[0]->Username)?>
                            </a></h6>
                        </div>
                    </div>  
                    <div class="m-3 mt-0">
                        <!-- Content -->
                        <div class="post-content">
                            <p class="m-1">
                                <?= htmlspecialchars_decode($post[0]->Content)?>
                            </p>
                            <?php if(isset($post[0]->Media)) : ?>
                                <?php foreach($post[0]->Media as $media) : ?>
                                    <?php if(isset($media['FilePath'])) : ?>
                                        <img src="<?= htmlspecialchars($media['FilePath'])?>" class="img-fluid" alt="Image"></img>
                                    <?php endif ;?>
                                <?php endforeach; ?>
                            <?php endif ;?>
                        </div>
                    </div>
                </div>
                <?php else:?>
                    <h4>Post could not be found</h4>
            <?php endif; ?>


            <!-- Comments go here -->
            <?php if(isset($_SESSION['Comments'])) : ?>
                <?php foreach($_SESSION['Comments'] as $comment) : ?>
                    <div class="container rounded-top border border-0-bottom bg-white mb-3">
                        <!-- Forum Post -->
                        <div class="p-3">
                            <div class="row align-items-center mb-2">

                                <h4 class="col text-start mb-0"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($comment['UserID']))?>/<?= urlencode($comment['Username'])?>/">
                                <?= htmlspecialchars($comment['Username'])?>
                                </a></h4>

                                <div class="col-auto ms-auto">
                                    <small class="text-muted">Posted on: <?= explode(" ",$comment['PublishDate'])[0]?></small>
                                </div>
                            </div>
                        </div>  
                        <div class="m-3 mt-0">
                            <!-- Content -->
                            <div class="post-content">
                                <p class="m-1">
                                    <?= htmlspecialchars_decode($comment['Content'])?>
                                </p>
                                <?php if(isset($comment['Media'])) : ?>
                                    <?php foreach($comment['Media'] as $media) : ?>
                                        <?php if(isset($media['FilePath'])) : ?>
                                            <img src="<?= htmlspecialchars($media['FilePath'])?>" class="img-fluid" alt="Image"></img>
                                        <?php endif ;?>
                                    <?php endforeach; ?>
                                <?php endif ;?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif ; ?>
            <?php if(isset($_SESSION['user'])) : ?>
                    <div class="container mt-3">
                        <h4 class="m-1">Create Comment</h4>
                        <form action="/Final-Project-Waltenberg/public/posts/<?= urlencode(base64_encode($post[0]->PostID))?>/<?= urlencode(slugify($post[0]->Title))?>/" method="POST">
                            <div class="mb-1">
                                <?php if (isset($errors['title'])) : ?> 
                                        <p class="text-xs mt-2" style="color: red;"><?= $errors['title']?></p>
                                <?php endif; ?>
                                
                            </div>
                            <div class="mb-1">
                                <label for="TextContent" class="form-label">Content</label>
                                <?php if (isset($errors['content'])) : ?> 
                                    <p class="text-xs mt-2" style="color: red;"><?= $errors['content']?></p>
                                <?php endif; ?>
                                <?php if (isset($errors['img'])) : ?> 
                                        <p class="text-xs mt-2" style="color: red;"><?= $errors['img']?></p>
                                <?php endif; ?>
                                <textarea class="form-control" name="tiny" id="tiny"></textarea>
                            </div>
                            <button type="submit" class="btn" style=" background-color: rgb(254, 250, 224);">Comment</button>
                        </form>
                    </div>
                <?php endif ;?>
        </main>
    </body>
</html>