<?php
namespace App\Controllers;

use App\functions;
use Backend\Models\Comment;
use Logger\LoggerFactory; 
use Backend\Models\User;
use DOMDocument;
use PDOException;
use Backend\Models\UserPost;

//The public entry index.
class CommentController {
    ///Entry screen for users.
    public function index($request, $response, $args) {
        
        $PostID = base64_decode($args['postID']);

        $config = require_once('../Backend/config.php');
        $userPostService = new UserPost($config['dungeon']);

        $userPostService->findSingleUserPost($PostID);
        if(isset( $_SESSION['SelectedPostJWT'])) {
            $Posts = [ '0' => functions\decodeJWT($_SESSION['SelectedPostJWT'])->content];
            $Posts = $userPostService->RetrievePostMedia($Posts);
            $_SESSION['SelectedPost'] = $Posts;
        

            $CommentService = new Comment($config['dungeon']);
            $comments = $CommentService->RetrieveCommentsWithID($Posts[0]->PostID);
            if($comments){
                $comments = $CommentService->RetrievePostMedia($comments); 
                $_SESSION['Comments'] = $comments;
            }
        }
        require __DIR__ . '/../Views/CommentView.php';
        $_SESSION['Comments'] = null;
        return $response;
    }

    //
    public function store($request, $response, $args) {
        $PostID = base64_decode($args['postID']);
        $slug = $args['slug'];

        $logger = LoggerFactory::getInstance()->getLogger();
        //clean and verify the data for bad inputs. (files, images, etc)
        $Content = $_POST['tiny'];
        if(isset($_SESSION['user'])){
            $user = $_SESSION['user'];
        }
        $errors = [];
        $processedImages = [];

        //check for null inputs
        if(!$Content){
            $errors['content'] = 'No content was added.';
        }
        if(!$user){
            $errors['title'] = 'User needed to create post';
        }
        if($errors){
            $_SESSION['errors'] = $errors;
            echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
            return $response->withStatus(404);
        }

         //grab the images from the content before sanitization. They will be checked over before submission individually. 
         $images = functions\parseIMGinHTML($Content);
 
         //UPDATE DOM AND STRIP OUT IMG's
         $dom = new DOMDocument();
         libxml_use_internal_errors(true);
         $dom->loadHTML($Content);
         libxml_clear_errors();
 
         // Get all <img> elements
         $images = $dom->getElementsByTagName('img');
         
         // Remove the <img> tag using regular expression (This is so the image isn't included in the html)
         $cleanInput = preg_replace('/<img[^>]*>/i', '', $Content);
         $Content = htmlspecialchars($cleanInput, ENT_QUOTES, 'UTF-8');

         //image handling.
        if($images){
            //use this to store processed images for later.
            $count = 0;
            foreach($images as $img) {
                
                //validate the input (Extract base64 image data using a preg_match and test the MIME)
                if (preg_match('/data:image\/(png|jpeg|jpg|gif|webp);base64,([A-Za-z0-9+\/=]+)/', $img->getAttribute('src'), $matches)) {
                    $imageType = $matches[1]; // png, jpeg, etc.
                    $imageData = $matches[2]; // base64 data

                } else {
                    $errors['img'] = 'Invalid Image Data.';
                    $_SESSION['errors'] = $errors;
                    echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
                    return $response->withStatus(415);
                }
                //sanitize
                $decodedImageData = base64_decode($imageData);
                if (!$decodedImageData || !getimagesizefromstring($decodedImageData)) {
                    $logger->error('Invalid or corrupted Image data in the post creation.');
                    $errors['img'] = 'Invalid or Corrupted Image Data.';
                    $_SESSION['errors'] = $errors;
                    echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
                    return $response->withStatus(400);
                }

                //Secure the filepath
                $folderPath =  'http://localhost/Final-Project-Waltenberg/Backend/Uploads/';
                
                $uploadPath = __DIR__ . '/../../Backend/Uploads/'; // Ensure this folder exists and is writable
                

                //generate the filename
                $fileName = uniqid('img_', true) . '.' . $imageType;
                // Generate a unique file name
                $filePath = $uploadPath . $fileName;

                //Add image for linking
                if (!file_put_contents($filePath, $decodedImageData)) {
                    $logger->error("Failed to save image.");
                    $errors['img'] = 'Invalid or Corrupted Image Data.';
                    $_SESSION['errors'] = $errors;
                    echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
                    return $response->withStatus(406);
                }

                //Validate the stored image
                if (!getimagesize($filePath)) {
                    unlink($filePath); // Remove corrupted file
                    $logger->error("Saved image failed to validate after saving.");
                    $errors['img'] = 'Saved image failed to validate after saving.';
                    $_SESSION['errors'] = $errors;
                    echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
                    return $response->withStatus(406);
                }
                $filePath = $folderPath . $fileName;

                $imgArray = [
                    'ID' => $count,
                    'fileName' => $fileName,
                    'FilePath' => $filePath
                ];
                array_push($processedImages, $imgArray);
                $count++;
            }
        } 
        try {
            //connect to database.
            $config = require_once('../Backend/config.php');
            $CommentService = new Comment($config['dungeon']);

            //Add data (title, content, data, userId of the creator(this can be pulled from the active-state SESSION) login required to create stuff)
            if(!$processedImages){
                $CommentService->AddComment( $Content, null, $user, $PostID);
            } else {
                $CommentService->AddComment( $Content, $processedImages, $user, $PostID);
            }
            $logger->info('Post was created.');
            echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
            return $response;
            
        } catch(PDOException $e) {
            $logger->error('There was an error connecting to the Database: ' . $e);
            $errors['title'] = 'There was an error connecting to the Database.';
            $_SESSION['errors'] = $errors;
            echo '<meta http-equiv="refresh" content="0 url="../posts/$PostID/$slug/" method="POST">';
            return $response->withStatus(503);
        }
    }
}