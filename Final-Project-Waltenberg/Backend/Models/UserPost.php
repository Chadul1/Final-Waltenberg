<?php 
namespace Backend\Models;

use Exception;
use PDO;
use PDOException;
use Logger\LoggerFactory;
use DateTime;
use functions;

use function App\functions\generateJWT;

class UserPost {

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

    ///Returns all of the user posts. 
    public function RetrieveUserPosts() {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('SELECT up.PostID, up.UserID, Username, Title, Content, PublishDate FROM userpost as up 
            INNER JOIN users as u on u.UserID = up.UserID 
            WHERE IsArchived = 0 and u.IsBanned = 0
            Order by PublishDate DESC');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $logger->info('List of user posts were retrieved.');
            
        } catch (PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
            $results = null; 
        }
        $_SESSION['PostsJWT'] = generateJWT($results);
    }


    //finds and returns all the posts that were made by a user. 
    public function FindUserPostsWithID($id){
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $logger->info('User posts that were attached to the user id were retrieved.');
            $stmt = $this->pdo->prepare('SELECT PostID, up.UserID, Username, Title, Content, PublishDate FROM userpost as up 
            INNER JOIN users as u on u.UserID = up.UserID 
            WHERE IsArchived = 0 and up.UserID = :UserID 
            Order by PublishDate DESC');
            $stmt->bindParam(':UserID', $id);
            $stmt->execute();
            $result = $stmt->fetchAll();

        } catch (PDOException $e) {
            $logger->error('There was an error trying to connect to the Database: ' . $e->getMessage());
            $result = null;
        }
        $_SESSION['UserPostsJWT'] = generateJWT($result);
    }



    //Adds a user post. 
    public function CreatePost($title, $content, $media = null, $user){
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $date = new DateTime('now');
            $date = $date->format('Y-m-d H:i:s');

            //checks if there's any processed media files, if so, the media files are added. 
            if(!$media){
                 //adds the user post to the db. 
                $stmt = $this->pdo->prepare('INSERT INTO userpost (UserID, Title, Content, PublishDate) VALUES (:userID, :title, :content, :datetime);');
                    $stmt->bindParam(':userID', $user['UserID']);
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':content', $content);
                    $stmt->bindParam(':datetime', $date);
                $stmt->execute();
                $logger->info('Comment was successfully added.');
            } else {
                //Adds the media along with the userPost. 
                $stmt = $this->pdo->prepare('INSERT INTO userpost (UserID, Title, Content, PublishDate) VALUES (:userID, :title, :content, :datetime)');
                    $stmt->bindParam(':userID', $user['UserID']);
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':content', $content);
                    $stmt->bindParam(':datetime', $date);
                    $stmt->execute();
                $PostID = $this->FindPostIDwithUseridTitleDate($user['UserID'], $title, $date);
                if($PostID) {
                    $this->AttachMedia($PostID['PostID'], $media);
                    $logger->info('Comment with media was successfully added.');
                } else {
                    $logger->error('There was an error trying to find the postID when attaching media.');
                }
            }
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the database: ' . $e);
        }
    }

    //inserts file paths of the comments into the database.
    public function AttachMedia($Id, $media) {
        $logger = LoggerFactory::getInstance()->getLogger();
        foreach($media as $row){
            try {
                $stmt = $this->pdo->prepare('INSERT INTO media (PostID, FileName, FilePath) VALUES (:postID, :fileName, :filePath)');
                    $stmt->bindParam(":postID", $Id, PDO::PARAM_INT);
                    $stmt->bindParam(":fileName", $row['fileName'], PDO::PARAM_STR);
                    $stmt->bindParam(":filePath", $row['FilePath'], PDO::PARAM_STR);
                $stmt->execute();
                $logger->info('Media was successfully added.');
                
            } catch(PDOException $e){
                $logger->error('There was an error adding the file: ' . $e);
            }
        }
 
    }

    //For finding the most recent userPost ID.
    public function FindPostIDwithUseridTitleDate($UserID, $Title, $date){
        $logger = LoggerFactory::getInstance()->getLogger(); 
        try {
            $stmt = $this->pdo->prepare('Select PostID from userpost where Title = :title and UserID = :userID and PublishDate = :datetime');
            $stmt->bindParam(':userID', $UserID);
            $stmt->bindParam(':title', $Title);
            $stmt->bindParam(':datetime', $date);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            $logger->error('There was an error trying to correct to the DB when finding the PostID: ' . $e);
            return null;
        } 
        
    }

    ///Returns all of the post media. 
    public function RetrievePostMedia($posts) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $tempArray = [];
                foreach ($posts as $post) {
                    $stmt = $this->pdo->prepare('select FileName, FilePath from media where PostID = :postID');
                    $stmt->bindParam(':postID', $post->PostID);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch associative arrays

                    if (empty($result)) {
                        $tempArray[] = $post; 
                    } else {
                        $mediaDetails = [];
                        foreach ($result as $row) {
                            $mediaDetails[] = ['FileName' => $row['FileName'], 'FilePath' => $row['FilePath']];
                        }
                        $post->Media = $mediaDetails; // Add media details to the post
                        $tempArray[] = $post;
                    }
                }
                return $tempArray;
        } catch(PDOException $e) {
            $logger->error('There was an error adding the file: ' . $e);
        }
    }

    //Finds and returns a single userPost. 
    public function findSingleUserPost($postID){
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $stmt = $this->pdo->prepare('SELECT up.PostID, up.UserID, Username, Title, Content, PublishDate FROM userpost as up 
            INNER JOIN users as u on u.UserID = up.UserID 
            WHERE IsArchived = 0 and up.PostID = :postID');
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch associative arrays
            
        } catch(PDOException $e) {
            $logger->error('There was an error when trying to find a single user post: ' . $e);
            $result = null; 
        }
        $_SESSION['SelectedPostJWT'] = generateJWT($result);
    }

    //Adds or removes flags depending on the user and post.
    public function FlagPost($user, $PostID) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $results = $this->FindFlags($PostID);
            if(empty($results)){
                try {
                    $stmt = $this->pdo->prepare('INSERT INTO flags (UserID, PostID, FlagStatus) VALUES (:userID, :postID, 1)');
                    $stmt->bindParam(':userID', $user['UserID']);
                    $stmt->bindParam(':postID', $PostID);
                    $stmt->execute();
                }  catch(PDOException $e) {
                    $logger->error('There was an error adding the flag: ' . $e);
                }
            } else {
                $counter = 0;
                foreach($results as $flag){
                    if($flag['UserID'] === $user['UserID']){
                        $counter++;
                    }      
                }
                if($counter != 0){
                    try {
                        $stmt = $this->pdo->prepare('DELETE FROM flags where UserID = :userId and PostId = :postId');
                        $stmt->bindParam(':userId', $user['UserID']);
                        $stmt->bindParam(':postId', $PostID);
                        $stmt->execute();
                    }  catch(PDOException $e) {
                        $logger->error('There was an error adding the flag: ' . $e);
                    }
                    
                } else {
                    try {
                        $stmt = $this->pdo->prepare('INSERT INTO flags(UserID, PostID, FlagStatus) VALUES (:userID, :postID, 1)');
                        $stmt->bindParam(':userID', $user['UserID']);
                        $stmt->bindParam(':postID', $PostID);
                        $stmt->execute();
                    }  catch(PDOException $e) {
                        $logger->error('There was an error adding the flag: ' . $e);
                    }
                }
            }

        } catch (Exception $e) {
            $logger->error('There was an error adding the flag: ' . $e);
        }
    }

    //finds the flags
    public function FindFlags($PostID) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $stmt = $this->pdo->prepare('Select PostID, UserID, FlagStatus from flags where PostID = :postID');
            $stmt->bindParam(':postID', $PostID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch associative arrays
        } catch(PDOException $e) {
            $logger->error('There was an error adding the file: ' . $e);
            return null;
        }
    }

    //Removes the flags from a post. 
    public function AdminFlagPost($PostID) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('DELETE FROM flags where PostId = :postId');
            $stmt->bindParam(':postId', $PostID);
            $stmt->execute();
            $logger->error('The flags were removed by an ADMIN.');
            return true;
        }  catch(PDOException $e) {
            $logger->error('There was an error removing the flags: ' . $e);
            return false;
        }
    }

    //Gets all the flagged posts and returns a single example so ensure there aren't duplicates.
    public function RetrieveFlaggedPosts() {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('SELECT DISTINCT  up.PostID, up.UserID, Username, Title, Content, PublishDate FROM userpost as up 
            INNER JOIN users as u on u.UserID = up.UserID
            INNER JOIN flags as f on f.PostID = up.PostID
            WHERE IsArchived = 0 and u.IsBanned = 0 and f.FlagStatus = 1');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $logger->info('List of flagged user posts were retrieved.');

        } catch (PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
            $results = null;
        }
        $_SESSION['AdminPostsJWT'] = generateJWT($results);
    }

    //Archives a selected post. 
    public function ArchivePost($PostID) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('UPDATE userpost SET IsArchived = 1 where PostID = :postID');
            $stmt->bindParam(':postID', $PostID);
            $stmt->execute();
            $logger->info('The post was archived.');
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
        }
    }

    //Gets all of the archived posts. 
    public function RetrieveArchivedPosts() {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('SELECT up.PostID, up.UserID, Username, Title, Content, PublishDate FROM userpost as up 
            INNER JOIN users as u on u.UserID = up.UserID
            WHERE IsArchived = 1 and u.IsBanned = 0');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $logger->info('List of user posts were retrieved.');
        } catch (PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
            $results = null;
        }
        $_SESSION['ArchivedPostsJWT'] = generateJWT($results);
    }

    //Pulls a post out of being archived;
    public function UnArchivePost($PostID) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('UPDATE userpost SET IsArchived = 0 where PostID = :postID');
            $stmt->bindParam(':postID', $PostID);
            $stmt->execute();
            $logger->info('The post was UnArchived.');
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
        }
    }
}   