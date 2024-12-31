<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
       <!-- Bootstrap CSS -->
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
    <body>
    <?php require('partials/navbar.php');?>
        <?php if(isset($_SESSION['user']['UserRole'])) : ?>
            <?php if($_SESSION['user']['UserRole'] === 'Admin') : ?>
            <div class="container my-5 shadow p-4 rounded">
                <form action="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($_SESSION['UserProfile']['UserID']))?>/<?= urlencode($_SESSION['UserProfile']['Username'])?>/" method="POST" class="d-flex align-items-center gap-3">
                    <label for="exampleSelect" class="form-label h3">Manage User</label>

                    <label for="UserRole" class="form-label mb-0">Role: </label>
                    <!-- B. Picker -->
                    <select class="form-select w-auto" id="UserRole" name="UserRole" required>
                        <option value="<?= $_SESSION['UserProfile']['UserRole']?>" selected ><?= $_SESSION['UserProfile']['UserRole']?></option>
                        <option value="Author">Author</option>
                        <option value="Admin">Admin</option>
                    </select>

                    <!-- C. Submit Button -->
                    <button type="submit" class="btn" style=" background-color: rgb(254, 250, 224);">Update</button>

                    <!-- D. Fourth Button -->
                    <button type="button" class="btn btn-danger"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                        href="/Final-Project-Waltenberg/public/ban/<?= urlencode(base64_encode($_SESSION['UserProfile']['UserID']))?>/<?= urlencode($_SESSION['UserProfile']['Username'])?>/">
                        Ban User </a></button>
                </form>
            </div>
            <?php endif ; ?>
        <?php endif ; ?>
        <main class="container my-5 shadow p-4 rounded">
            <!-- User Details -->
            <h2 id="username" class="m-3"> 
                <?php echo isset($_SESSION['UserProfile']) ? $_SESSION['UserProfile']['Username'] . '\'s' : 'Guest' ?> Profile</h2>

            <div class="row m-1">
                <h4 class="col-1">Posts</h4>
            </div>

            <?php 
                require('partials/ProfilePosts.php');
            ?>
        </main>
    </body>
</html>
