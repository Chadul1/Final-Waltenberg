<?php 
namespace Backend\BackendControllers;
use Backend\Models\User;

class UserController {

    ///entry screen for the backend of the users. 
    public function index($request, $response, $args) {

        require  __DIR__ . '/../Views/UserIndex.View.php';
        return $response;
    }

    //Returns a List of Banned Users
    public function bans($request, $response, $args){
        $config = require_once __DIR__ . "/../config.php";
        $userService = new user($config['dungeon']);
        $result = $userService->findBannedUsers();

        if($result) {
            $_SESSION['BannedUsers'] = $result;
        }
        require_once __DIR__ . '/../Views/Ban.View.php';
        return $response->withStatus(200);
    }

     //shows banned users.
     public function unban($request, $response, $args){
        $userID = base64_decode($args['UserId']);
        
        $config = require_once __DIR__ . "/../config.php";
        $userService = new user($config['dungeon']);
        $userService->UnbanUser($userID);

        echo '<meta http-equiv="refresh" content="0;url=../" method="POST">';
        return $response->withStatus(200);
        
    }
}