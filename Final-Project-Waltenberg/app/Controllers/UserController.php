<?php
namespace App\Controllers;

use Backend\Models\UserPost;
use Backend\Models\User;
use app\functions;

//The public entry index.
class UserController {

    ///entry screen for the backend of the users. 
    public function index($request, $response, $args) {
        //finds the user for display
        $userID = base64_decode($args['UserID']);
        $config = require_once('../Backend/config.php');
        $postService = new UserPost($config['dungeon']);
        $postService->FindUserPostsWithID($userID);


        if(isset($_SESSION['UserPostsJWT'])){
            $posts = functions\decodeJWT($_SESSION['UserPostsJWT']);
            $posts = $postService->RetrievePostMedia($posts->content); 
            $_SESSION['UserPosts'] = $posts;
        }

        //finds the user for display.
        $userService = new User($config['dungeon']);
        $user = $userService->findUserWithId($userID);
        if($user) {
            $_SESSION['UserProfile'] = $user;
        }

        require __DIR__ . '/../Views/UserView.php';
        return $response->withStatus(200);
    }

    //Updates the inputted user role.
    public function store($request, $response, $args) {
        $userID = base64_decode($args['UserID']);
        $userRole = htmlspecialchars($_POST['UserRole']);

        $config = require_once __DIR__ . "/../../Backend/config.php";
        $userService = new user($config['dungeon']);
        $userService->UpdateUserRole($userID, $userRole); 

        require __DIR__ . '/../Views/UserView.php';
        return $response->withStatus(200);
    }

    //Bans a user.
    public function ban($request, $response, $args){
        $userID = base64_decode($args['UserID']);
        $config = require_once __DIR__ . "/../../Backend/config.php";
        $userService = new user($config['dungeon']);
        $userService->BanUser($userID);

        require __DIR__ . '/../Views/UserView.php';
        return $response->withStatus(200);
    }
}