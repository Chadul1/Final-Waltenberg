<?php 
namespace Backend\Models;

use PDO;
use DateTime;
use Directory;
use PDOException;
use Logger\LoggerFactory;

//The comment model for moving and standardizing model data.
class Comment {

    //for connecting to the DB. 
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

    //Finds and returns all of the comments that are attached to a userPost.
    public function RetrieveCommentsWithID($Id) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('Select c.CommentID, c.UserID, Username, Content, PublishDate from comments as c 
            INNER JOIN users as u on u.UserID = c.UserID 
            where PostID = :postID and IsArchived = 0;');
            $stmt->bindParam(':postID', $Id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $logger->info('Comment was retrieved and found.');

            return $result;
        } catch (PDOException $e) {
            $logger->error('There was an error connecting to the DB: ' . $e->getMessage());
            return null;
        }
    }

    //Adds a user comment.
    public function AddComment($content, $media=null, $user, $postID){
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $date = new DateTime('now');
            $date = $date->format('Y-m-d H:i:s');

            //checks if there's any processed media files, if so, the media files are added. 
            if(!$media){
                //adds the user post to the db. 
                $stmt = $this->pdo->prepare('INSERT into comments (PostID, UserID, Content, PublishDate) VALUES (:postID ,:userID, :content, :datetime);');
                    $stmt->bindParam(':postID', $postID);
                    $stmt->bindParam(':userID', $user['UserID']);
                    $stmt->bindParam(':content', $content);
                    $stmt->bindParam(':datetime', $date);
                $stmt->execute();
                $logger->info('Comment was successfully added.');
            } else {
                //Adds the media along with the userPost. 
                $stmt = $this->pdo->prepare('INSERT INTO comments (PostID, UserID, Content, PublishDate) VALUES (:postID ,:userID, :content, :datetime);');
                    $stmt->bindParam(':postID', $postID);
                    $stmt->bindParam(':userID', $user['UserID']);
                    $stmt->bindParam(':content', $content);
                    $stmt->bindParam(':datetime', $date);
                    $stmt->execute();
                $CommentID = $this->FindPostIDwithUseridDate($user['UserID'], $date);
                if($CommentID) {
                    $this->AttachMedia($CommentID['CommentID'], $media);
                    $logger->info('Comment with media was successfully added.');
                } else {
                    $logger->error('There was an error trying to find the CommendID.');

                }
            }
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the database: ' . $e);
        }
    }

    //Inserts file paths of the comments into the database.
    public function AttachMedia($Id, $media) {
        $logger = LoggerFactory::getInstance()->getLogger();
        foreach($media as $row){
            try {
                var_dump($Id);
                $stmt = $this->pdo->prepare('INSERT INTO media (CommentID, FileName, FilePath) VALUES (:commentID, :fileName, :filePath);');
                    $stmt->bindParam(':commentID', $Id);
                    $stmt->bindParam(':fileName', $row['fileName']);
                    $stmt->bindParam(':filePath', $row['FilePath']);
                $stmt->execute();
                $logger->info('Media was successfully added.');
            } catch(PDOException $e){
                $logger->error('There was an error adding the media file: ' . $e);
            }
        }
    }

    //For finding the most recent userPost ID.
    public function FindPostIDwithUseridDate($UserID, $date){
        $logger = LoggerFactory::getInstance()->getLogger();
        try {
            $stmt = $this->pdo->prepare('Select CommentID from comments where UserID = :userID and PublishDate = :datetime');
                $stmt->bindParam(':userID', $UserID);
                $stmt->bindParam(':datetime', $date);
                $stmt->execute();
                $logger->info('The postID was found and returned.');
            return $stmt->fetch();
        } catch(PDOException $e) {
            $logger->error('There was an error trying to find and return the postID by UID and date: ' . $e->getMessage());
            return null;
        }
        
    }

    ///Returns all of the post media attached to a Comment.
    public function RetrievePostMedia($comments) {
        $logger = LoggerFactory::getInstance()->getLogger();
        try{
            $tempArray = [];
            foreach($comments as $comment) {
                $stmt = $this->pdo->prepare('select FileName, FilePath from media where CommentID = :commentID');
                $stmt->bindParam(':commentID', $comment['CommentID']);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch associative arrays

                if (empty($result)) {
                    $tempArray[] = $comment; 

                } else {
                    $mediaDetails = [];
                    foreach ($result as $row) {
                        $mediaDetails[] = ['FileName' => $row['FileName'], 'FilePath' => $row['FilePath']];
                    }
                    $comment['Media'] = $mediaDetails; // Add media details to the post
                    
                    $tempArray[] = $comment;
                }
            }
            return $tempArray;

        } catch(PDOException $e) {
            $logger->error('There was an error adding the file: ' . $e);
        }
    }
}