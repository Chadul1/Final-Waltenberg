<?php
namespace App\Controllers;

use App\functions;
use Logger\LoggerFactory; 
use Backend\Models\User;
use PDOException;

//The public entry index.
class LoginController {

    ///entry screen for the login page. 
    public function index($request, $response, $args) {
        
        require(__DIR__ . '/../Views/LoginView.php');
        return $response;
    }

    //Gets the post and verifies it. 
    public function store($request, $response, $args) {
        $logger = LoggerFactory::getInstance()->getLogger();
        $logger->info('The user has submitted the login form.');
        

        $config = require_once('../Backend/config.php');

        $errors = [];

        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        try {
            $userService = new User($config['dungeon']);
            $logger->info('The database was contacted to search for username');
            $user = $userService->findUserWithName($username);
            
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the database: ' . $e->getMessage());
            $errors = [
                'username' => 'There was an error connecting to the database'
            ];
            $_SESSION['errors'] = $errors;
            echo '<meta http-equiv="refresh" content="0;url=../public/login" method="POST">';
            return $response->withStatus(503);
        }

        //checks the user, then the password
        if(!isset($user)) {
            $errors = [
                'username'=> 'Username Not Found',
            ];
            $logger->warning('No username was found.');
            $_SESSION['errors'] = $errors;
            echo '<meta http-equiv="refresh" content="0;url=../public/login" method="POST">';
            return $response->withStatus(404);
        } else {
            if ($userService->LoginWithUsername($username, $password)){
                functions\login($user);
                $logger->info('The password was valid and the user was logged in.');
                echo '<meta http-equiv="refresh" content="0;url=../public/" method="POST">';
                return $response->withStatus(200);
            } else {
                $errors = [ 
                    'password'=> 'Incorrect password!',
                ];
                $logger->info('The inputted password was invalid.');
                $_SESSION['errors'] = $errors;
                echo '<meta http-equiv="refresh" content="0;url=../public/login" method="POST">';
                return $response->withStatus(400);
            }
        }
    }

    //Destroys the session upon logout.
    public function destroy($request, $response, $args) {
        $logger = LoggerFactory::getInstance()->getLogger();
        $logger->info('The user has logged out.');
        functions\logout();
        echo '<meta http-equiv="refresh" content="0;url=../public">';
        return $response->withStatus(200);
    }
}