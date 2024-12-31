
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Basic HTML layout">
    <!-- Replace with the username of the selected user.-->
    <title>Banned Users</title>
    <!-- Include Bootstrap for styling (optional) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>  
</head>
    <body style="display: flex; flex-direction: column; min-height: 100vh; ">
        <?php require_once('partials/adminNavbar.php') ?>
        <main class="container my-2">
            <h1>Banned Users</h1>
            <!-- List oif banned users -->
            <?php if(isset($_SESSION['BannedUsers'])): ?>
                <?php foreach($_SESSION['BannedUsers'] as $row) :?>
                    <div class="container rounded-top border border-0-bottom bg-white mb-3">
                        <!-- Forum Post -->
                        <div class="p-3">
                        <!-- Header -->
                            <div class="row align-items-center mb-2">
                            <h4 class="col text-start mb-0"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                    href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($row['UserID']))?>/<?= urlencode($row['Username'])?>/">
                                    <?= htmlspecialchars($row['Username']) ?></a></h4>
                                <div class="col-auto ms-auto">
                                    <button type="button" class="btn" style=" background-color: rgb(254, 250, 224);"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                    href="/Final-Project-Waltenberg/backend/bans/<?= urlencode(base64_encode($row['UserID']))?>/">
                                    Unban User </a></button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ; ?>
            <?php else : ?>
                <h1>There are no Banned users at this time</h1>
            <?php endif; ?>
        </main> 
    </body>
</html>
<?php 
        if(isset($_SESSION['BannedUsers'])){
            $_SESSION['BannedUsers'] = null;
        }
    ?> 