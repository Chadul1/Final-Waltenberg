<?php 
namespace Backend\Models;

use PDO;
use PDOException;
use Logger\LoggerFactory;

class User{

    private $pdo;

    //Creates a new instance of the userPost Class. 
    public function __construct($config, $username = 'root', $password = '') {
        //using the config array file, the dsn connection is created using a build_query helper action. 
        $dsn = 'mysql:' . http_build_query($config, '', ';');
        
        //the connection is created and set for fetching to help with defaulting errors when connecting. 
        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    //adds a user.
    public function AddUser($username, $password) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $logger->info('User was added.');
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('INSERT INTO users(username, password) VALUES (:username, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
        } catch (PDOException $e) {
            $logger->error('There was an error trying to connect to the Database: ' . $e->getMessage());
        }
    }

    //Finds a user by the ID.
    public function findUserWithId($Id) {
        $logger = LoggerFactory::getInstance()->getLogger();

        try {
            $query = "Select UserID, Username, UserRole, IsBanned from users where UserID = :UserID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':UserID', $Id);
            $stmt->execute();
            $result = $stmt->fetch();

            $logger->info('Successful Connection to Database');

            return $result;
        } catch (PDOException $e) {
            $logger->error('There was a PDO error when trying to find a User by their UserID: ' . $e->getMessage());
            return null; 
        }
    }

    //Finds a user by their username.
    public function findUserWithName($Username){
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $query = "Select UserID, Username, UserRole, IsBanned from users where Username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':username', $Username);
            $stmt->execute();
            $result = $stmt->fetch();

            $logger->info('Successful Connection to Database');
            return $result;
        } catch (PDOException $e) {

            $logger->error('There was a PDO error when trying to find a User by their username: ' . $e->getMessage());
            return null; 
        }
    }

    //allows a user to log in if their credentials are all in order.
    public function LoginWithUsername($Username, $password) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $query = "Select Username, Password from users where Username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':username', $Username);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if(password_verify($password, $result['Password'])){
                $logger->info('The password is correct.');
                return true;
            } else {
                $logger->info('The inputted password was incorrect.');
                return false;
            }
        } catch(PDOException $e){
            
            $logger->error('There was a PDO error when trying to find a User by their username: ' . $e->getMessage());
            return false; 
        }
    }

    //finds and returns all of the banned users
    public function findBannedUsers(){
        try{
            $stmt = $this->pdo->prepare('Select UserID, Username, IsBanned from users where IsBanned = 1');
            $stmt->execute();
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            $logger = LoggerFactory::getInstance()->getLogger();
            $logger->error('There was a PDO error when trying to find banned users: ' . $e->getMessage());
            return null;
        }
    }

    //Unbans a selected User
    public function UnbanUser($UserId){
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $stmt = $this->pdo->prepare('UPDATE users SET IsBanned=0 where UserID = :userID');
            $stmt->bindParam(':userID', $UserId);
            $stmt->execute();
            $logger->info('User was Unbanned');
        } catch(PDOException $e) {
            $logger->error('There was a PDO error when trying to unban a user: ' . $e->getMessage());
        }
    }

    //Bans a selected User.
    public function BanUser($UserId){
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $stmt = $this->pdo->prepare('UPDATE users SET IsBanned=1 where UserID = :userID');
            $stmt->bindParam(':userID', $UserId);
            $stmt->execute();
            $logger->info('User was banned');
        } catch(PDOException $e) {
            $logger->error('There was a PDO error when trying to ban a user: ' . $e->getMessage());
        }
    }

    //Updates the user role. 
    public function UpdateUserRole($userID, $userRole) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $stmt = $this->pdo->prepare('UPDATE users SET UserRole = :userRole where UserID = :userID');
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':userRole', $userRole);
            $stmt->execute();
            $logger->info('The userRole was updated.');
        } catch(PDOException $e) {
            $logger->error('There was a PDO error when trying to update a user: ' . $e->getMessage());
        }
    }
}