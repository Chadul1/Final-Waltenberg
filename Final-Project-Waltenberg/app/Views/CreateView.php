<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Replace with the Title of the post. -->
    <title>Create New Post</title>
    <?php
    // Check for errors in the session and display them
    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];

        // Clear the errors after displaying them
        unset($_SESSION['errors']);
    }?>
       <!-- Bootstrap CSS -->
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
            });
        </script>
    </head>
    <body>
        <?php require('partials/navbar.php');?>
            <div class="container">
                <h1 class="m-3">Create Post</h1>
                <form action="/Final-Project-Waltenberg/public/posts/submit" method="POST">
                    <div class="mb-3">
                        <label for="TitleInput" class="form-label">Title</label>
                        <?php if (isset($errors['title'])) : ?> 
                                <p class="text-xs mt-2" style="color: red;"><?= $errors['title']?></p>
                            <?php endif; ?>
                        <input type="Title" class="form-control" name="TitleInput" id="TitleInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="TextContent" class="form-label">Content</label>
                        <?php if (isset($errors['content'])) : ?> 
                            <p class="text-xs mt-2" style="color: red;"><?= $errors['content']?></p>
                        <?php endif; ?>
                        <?php if (isset($errors['img'])) : ?> 
                                <p class="text-xs mt-2" style="color: red;"><?= $errors['img']?></p>
                        <?php endif; ?>
                        <textarea class="form-control" name="tiny" id="tiny"></textarea>
                    </div>
                    <button type="submit" class="btn" style=" background-color: rgb(254, 250, 224);">Create</button>
                </form>
            </div>
        </div>
    </body>
</html>