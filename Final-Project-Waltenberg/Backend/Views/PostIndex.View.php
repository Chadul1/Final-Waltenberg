<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Basic HTML layout">
    <title>Flagged Posts</title>
    <!-- Include Bootstrap for styling (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php use function App\functions\slugify;?>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; ">
    <?php require_once('partials/adminNavbar.php') ?>
        <main class="container my-2">
            <h1>Most Recent Flagged Comments</h1>
            <!-- The post display -->
            <?php if(isset($_SESSION['AdminPosts'])): ?>
                <?php foreach($_SESSION['AdminPosts'] as $row) :?>
                    <div class="container rounded-top border border-0-bottom bg-white mb-3">
                        <!-- Forum Post -->
                        <div class="p-3">
                            <div class="row align-items-center mb-2">
                            <h4 class="col text-start mb-0"><a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                    href="/Final-Project-Waltenberg/public/posts/<?= base64_encode($row->PostID)?>/<?= urlencode(slugify($row->Title))?>/">
                                    <?= htmlspecialchars_decode($row->Title) ?></a></h4>
                                <div class="col-auto ms-auto">
                                    <small class="text-muted">Posted on: <?= explode(" ",$row->PublishDate)[0]?></small>
                                </div>
                                <div class="col-auto ms-auto">
                                    <?php if(isset($_SESSION['user']['UserRole'])) : ?>
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle border" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style=" background-color: rgb(254, 250, 224);"></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="/Final-Project-Waltenberg/backend/posts/flag/<?= base64_encode($row->PostID)?>/<?= urlencode(slugify($row->Title))?>/">Remove Flags</a></li>
                                                <?php if($_SESSION['user']['UserRole'] === 'Admin') : ?>
                                                    <li><a class="dropdown-item" href="/Final-Project-Waltenberg/backend/posts/archive/<?= base64_encode($row->PostID)?>/<?= urlencode(slugify($row->Title))?>/">Archive</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-2">
                                <h6 class="text-muted">by <a class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                                    href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($row->UserID))?>/<?= urlencode($row->Username)?>/">
                                    <?= htmlspecialchars($row->Username)?>
                                </a></h6>
                            </div>
                        </div>  
                        <div class=" p-3 ">
                            <!-- Content -->
                            <div class="post-content">
                                <p>
                                    <?= htmlspecialchars_decode($row->Content)?>
                                </p>
                                <?php if(isset($row->Media)) : ?>
                                        <?php foreach($row->Media as $media) : ?>
                                            <?php if(isset($media['FilePath'])) : ?>
                                                <img src="<?= htmlspecialchars($media['FilePath'])?>" alt="Image"></img>
                                            <?php endif ;?>
                                        <?php endforeach; ?>
                                    <?php endif ;?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- Is there is nothing to display -->
                <?php else : ?>
                    <h1>There are no Reported Posts at this time</h1>
            <?php endif; ?>
        </main>
    </body>
</html>


