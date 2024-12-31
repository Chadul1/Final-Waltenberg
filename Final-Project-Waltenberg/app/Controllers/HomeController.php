<?php
namespace App\Controllers;

use Backend\Models\UserPost;
use App\functions;

//The public entry index.
class HomeController {

    ///Entry screen for users.
    public function index($request, $response, $args) {
        $config = require_once('../Backend/config.php');
        $userPostService = new UserPost($config['dungeon']);
        $userPostService->RetrieveUserPosts();
        
        if(isset($_SESSION['PostsJWT'])){
            $posts = $_SESSION['PostsJWT'];
            $posts = functions\decodeJWT($posts);
            $posts = $userPostService->RetrievePostMedia($posts->content); 
            $_SESSION['Posts'] = $posts;
        }

        require __DIR__ . '/../Views/HomeView.php';
        return $response->withStatus(200);
    }
}