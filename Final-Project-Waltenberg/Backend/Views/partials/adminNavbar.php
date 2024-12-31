<nav class="navbar navbar-expand-lg navbar-light" style=" background-color: rgb(254, 250, 224);">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Final-Project-Waltenberg/public/">ChatDungeon</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/Final-Project-Waltenberg/backend/posts/">Manage Flagged Posts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Final-Project-Waltenberg/backend/bans/">Manage Bans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Final-Project-Waltenberg/backend/archives/">Manage Archives</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 20px;">    
                        Welcome <?php echo isset($_SESSION['user']) ?  $_SESSION['user']['Username'] : 'Guest';?>
                    </a>
                    <ul class="dropdown-menu m-2" aria-labelledby="navbarDropdownMenuLink" >
                        <?php if(isset($_SESSION['user'])) : ?>
                            <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/users/<?= urlencode(base64_encode( $_SESSION['user']['UserID']))?>/<?= urlencode( $_SESSION['user']['Username'])?>/">Profile</a></li>

                            <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/logout">Logout</a></li>
                        <?php endif; ?>
                        <?php if(!isset($_SESSION['user'])) : ?>
                            <li><a class="dropdown-item" href="/Final-Project-Waltenberg/public/login">Login/Signup</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

  
