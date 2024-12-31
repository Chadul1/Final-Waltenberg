<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
       <!-- Bootstrap CSS -->
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php
    // Check for errors in the session and display them
    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];

        // Clear the errors after displaying them
        unset($_SESSION['errors']);
    }?>
</head>
    <body>
    <?php 
        require('partials/navbar.php');
    ?>
        <div class="d-flex justify-content-center align-items-center full-height pt-5 mt-5">
            <div class="card" style="width: 22rem; background-color: rgb(254, 250, 224);">
                <div class="card-body text-center"> <!-- Added text-center for centering content -->
                    <h2 class="card-title mb-4">Login to Account</h2>
                    <form action="/Final-Project-Waltenberg/public/login" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username </label>
                            <input id="username" name="username" type="username" class="form-control bg-light text-dark border-0" placeholder="Enter username" maxlength="20"> 
                            <div id="error-message" style="color: red;"></div>
                            
                            <?php if (isset($errors['username'])) : ?> 
                                <p class="text-xs mt-2" style="color: red;"><?= $errors['username']?></p>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" class="form-control bg-light text-dark border-0" placeholder="Enter password" maxlength="20">
                            <div id="error-message" style="color: red;"></div>

                            <?php if (isset($errors['password'])) : ?> 
                                <p class="text-xs mt-2" style="color: red;"><?= $errors['password']?></p>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn w-100" style="background-color: white;">Login</button>
                        <div class="mt-3 text-center">
                            <small>Don't have an account? <a href="/Final-Project-Waltenberg/public/register" style="text-decoration: none;">Sign up</a></small>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
    </body>
</html>