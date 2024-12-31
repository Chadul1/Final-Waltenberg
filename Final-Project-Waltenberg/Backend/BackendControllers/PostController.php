<?php 
namespace Backend\BackendControllers;

use Backend\Models\UserPost;
use Firebase\JWT;
use app\functions;

use function App\functions\decodeJWT;

class PostController {

    ///entry screen for the backend of the user posts. 
    public function index($request, $response, $args) {
    
        $config = require_once __DIR__ . "/../config.php";
        $userPostService = new UserPost($config['dungeon']);
        $userPostService->RetrieveFlaggedPosts();
        
        if(isset($_SESSION['AdminPostsJWT'])){
            $posts = $_SESSION['AdminPostsJWT'];
            $posts = decodeJWT($posts);
            $posts = $userPostService->RetrievePostMedia($posts->content); 
            $_SESSION['AdminPosts'] = $posts;
        }

        require_once __DIR__ . '/../Views/PostIndex.View.php';
        $_SESSION['AdminPosts'] = null;
        return $response->withStatus(200);
    }

     //Removes all of the flags from a selected post.
     public function flag($request, $response, $args){
        $PostID = base64_decode($args['postID']);

        //connect to database.
        $config = require_once __DIR__ . "/../config.php";
        $userPostService = new UserPost($config['dungeon']);
        $result = $userPostService->AdminFlagPost($PostID);
        if($result === true){
            echo '<meta http-equiv="refresh" content="0;url=../../../" method="POST">';
            return $response->withStatus(200);
        }
        echo '<meta http-equiv="refresh" content="0;url=../../../" method="POST">';
        return $response->withStatus(400);
    }

    //Returns a list of archived posts. 
    public function archives($request, $response, $args){

        $config = require_once __DIR__ . "/../config.php";
        $userPostService = new UserPost($config['dungeon']);
        $userPostService->RetrieveArchivedPosts();
        
        if(isset($_SESSION['ArchivedPostsJWT'])){
            $posts = functions\decodeJWT( $_SESSION['ArchivedPostsJWT']);
            $posts = $userPostService->RetrievePostMedia($posts->content); 
            $_SESSION['ArchivedPosts'] = $posts;
        }

        require_once __DIR__ . '/../Views/archived.view.php';
        $_SESSION['ArchivedPosts'] = null;
        return $response->withStatus(200);
    }

    //Archives a selected post.
    public function archive($request, $response, $args){
        $PostID = base64_decode($args['postID']);

        $config = require_once __DIR__ . "/../config.php";
        $userPostService = new UserPost($config['dungeon']);
        $userPostService->ArchivePost($PostID);

        echo '<meta http-equiv="refresh" content="0;url=../../../" method="POST">';
        return $response->withStatus(200);
    }

    
    //UnArchives a Selected Post
    public function UnArchive($request, $response, $args) {
        $PostID = base64_decode($args['postID']);
        $config = require_once __DIR__ . "/../config.php";
        $userPostService = new UserPost($config['dungeon']);
        $userPostService->UnArchivePost($PostID);

        echo '<meta http-equiv="refresh" content="0;url=../../" method="POST">';
        return $response->withStatus(200);
    }
}