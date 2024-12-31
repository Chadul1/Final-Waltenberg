<?php
namespace App\Controllers;

use Logger\LoggerFactory; 
use Backend\Models\User;
use app\functions;
use PDOException;

//The public entry index.
class RegisterController {

    ///entry register screen for the users. 
    public function index($request, $response, $args) {
        
        require __DIR__ . '/../Views/RegisterView.php';
        return $response;
    }

    //The post that adds the user.
    public function store($request, $response, $args) {
        $logger = LoggerFactory::getInstance()->getLogger();
        $logger->info('The user has submitted their account registration.');

        require __DIR__ . '/../Views/RegisterView.php';
        $config = require_once('../Backend/config.php');

        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $userService = new User($config['dungeon']);
        $errors = $this->authenticate($username, $password);

        if(!empty($errors)){
            $_SESSION['errors'] = $errors;
            $logger->error('The inputted registration wasn\'t valid.');
            echo '<meta http-equiv="refresh" content="0; url=../public/register" method="POST">'; 
            return $response->withStatus(400);
        } 
        try {
            $result = $userService->findUserWithName($username);
        } catch (PDOException $e) {
            //redirects with database error.
            $errors = [
                'username' => 'There was an error connecting to the database'
            ];
            $_SESSION['errors'] = $errors;
            //logger actions.
            $logger->error('There was an error connecting to the database.');
            //redirect
            echo '<meta http-equiv="refresh" content="0;url=../public/register" method="POST">';
            return $response->withStatus(400);
        }

        if($result){
            $error['username'] = "Account already exists with that username, please choose another.";
            $_SESSION['errors'] = $errors;
            //logger actions.
            $logger->warning('The account name already existed.');
            //redirect.
            echo '<meta http-equiv="refresh" content="0; url=../public/register" method="POST">';
            return $response->withStatus(400);
        } else {
            try {
                $userService->addUser($username, $password);
                $user = $userService->findUserWithName($username);
                
                $logger->info('The account was added.');
            
                Functions\login($user);
            
                echo '<meta http-equiv="refresh" content="0;url=../public">';
                return $response->withStatus(200);

            } catch(PDOException $e) {
                //redirects with database error.
                $errors = [
                    'username' => 'There was an error connecting to the database'
                ];
                $_SESSION['errors'] = $errors;
                //logger actions.
                $logger->error('There was an error connecting to the database.');
                //redirect
                echo '<meta http-equiv="refresh" content="0;url=../public/register" method="POST">';
                return $response->withStatus(503);
            }
        }
    }

    //Checks if the username and password are valid before the JWT is created. This is for security sake.
    public function authenticate($username, $password) {
        $logger = LoggerFactory::getInstance()->getLogger();
        
        //Set up  error validation. 
        $errors = [];

        $validateName = Functions\validateUsername($username);
        if ($validateName !== "valid") {
            $errors['username'] = $validateName;
            $logger->warning('The inputted username wasn\'t valid.');
        }

        $validatePassword = Functions\validatePassword($password);
        if($validatePassword !== "valid") {
            $errors['password'] = $validatePassword;
            $logger->warning('The inputted password wasn\'t valid.');
        }

        return $errors;
    }
}