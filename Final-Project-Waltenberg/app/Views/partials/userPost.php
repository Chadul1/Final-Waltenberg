<?php use function app\functions\slugify; ?>
    <?php if(isset($_SESSION['Posts'])): ?>
        <?php foreach($_SESSION['Posts'] as $row) :?>
            <div class="container rounded-top border border-0-bottom bg-white mb-3">
                <!-- Forum Post -->
                <div class="p-3">
                <!-- Header -->
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
                                        <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/posts/flag/<?= base64_encode($row->PostID)?>/<?= urlencode(slugify($row->Title))?>/">Report</a></li>
                                        <?php if($_SESSION['user']['UserRole'] === 'Admin'): ?>
                                        
                                            <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/posts/archive/<?= base64_encode($row->PostID)?>/<?= urlencode(slugify($row->Title))?>/">Archive</a></li>
                                        <?php endif ;?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-2">
                        <h6 class="text-muted">by <a class="link-dark  link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" 
                        href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode($row->UserID))?>/<?= urlencode($row->Username)?>/">
                        <?= htmlspecialchars($row->Username)?>
                        </a></h6>
                    </div>
                </div>  
                <div class="p-3">
                    <!-- Content -->
                    <div class="post-content">
                        <p class="m-1">
                            <?= htmlspecialchars_decode($row->Content)?>
                        </p>
                        <?php if(isset($row->Media)) : ?>
                            <?php foreach($row->Media as $media) : ?>
                                <?php if(isset($media['FilePath'])) : ?>
                                    <img src="<?= htmlspecialchars($media['FilePath'])?>" class="img-fluid" alt="Image"></img>
                                <?php endif ;?>
                            <?php endforeach; ?>
                        <?php endif ;?>
                    </div>
                </div>
            </div>  
        <?php endforeach; ?>
        <!-- Is there is nothing to display -->
        <?php else : ?>
            <h1>There are no Posts to display at this time</h1>
    <?php endif; ?>