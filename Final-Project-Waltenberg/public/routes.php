<?php
use App\Controllers;
use Backend\BackendControllers;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Psr\Http\Message\RequestInterface;

use function App\Middleware\roleMiddleware;

$app->setBasePath('/Final-Project-Waltenberg');

//The homepage
$app->get('/public/', Controllers\HomeController::class . ':index');

//Profile Page and User Update System
$app->get('/public/users/{UserID}/{slug}/', Controllers\UserController::class . ':index');
$app->post('/public/users/{UserID}/{slug}/', Controllers\UserController::class . ':store')->add(roleMiddleware(['Admin']));

//Profile page ban button.
$app->get('/public/ban/{UserID}/{slug}/', Controllers\UserController::class . ':ban')->add(roleMiddleware(['Admin']));

//For creating comments on auto pages.
$app->get('/public/posts/{postID}/{slug}/', Controllers\CommentController::class . ':index');
$app->post('/public/posts/{postID}/{slug}/', Controllers\CommentController::class . ':store');
//For flagging posts
$app->get('/public/posts/flag/{postID}/{slug}/', Controllers\PostController::class . ':flag');
//For archiving posts
$app->get('/public/posts/archive/{postID}/{slug}/', Controllers\PostController::class . ':archive');


//For creating posts
$app->get('/public/posts/submit', Controllers\PostController::class . ':index')->add(roleMiddleware(['Author', 'Admin']));
$app->post('/public/posts/submit', Controllers\PostController::class . ':store')->add(roleMiddleware(['Author', 'Admin']));

//For creating a new user.
$app->get('/public/register', Controllers\RegisterController::class . ':index');
$app->post('/public/register', Controllers\RegisterController::class . ':store');

//For creating a new user.
$app->get('/public/login', Controllers\LoginController::class . ':index');
$app->post('/public/login', Controllers\LoginController::class . ':store');
//Logging out.
$app->get('/public/logout', Controllers\LoginController::class . ':destroy')->add(roleMiddleware(['Author', 'Admin']));

//ADMIN ACTIONS

//Admin flagged post management page.
$app->get('/backend/posts/', BackendControllers\PostController::class . ':index')->add(roleMiddleware(['Admin']));

//Admin Archives post management page.
$app->get('/backend/archives/', BackendControllers\PostController::class . ':archives')->add(roleMiddleware(['Admin']));
$app->get('/backend/archives/{postID}/{slug}/', BackendControllers\PostController::class . ':UnArchive')->add(roleMiddleware(['Admin']));

//Admin Archive and flag ADMIN actions management page.
$app->get('/backend/posts/flag/{postID}/{slug}/', BackendControllers\PostController::class . ':flag')->add(roleMiddleware(['Admin']));
$app->get('/backend/posts/archive/{postID}/{slug}/', BackendControllers\PostController::class . ':archive')->add(roleMiddleware(['Admin']));

//Admin user ban management page
$app->get('/backend/bans/', BackendControllers\UserController::class . ':bans')->add(roleMiddleware(['Admin']));
$app->get('/backend/bans/{UserId}/', BackendControllers\UserController::class . ':unban')->add(roleMiddleware(['Admin']));


// Set the custom handler for the errors in case of an out of bounds search for graceful 404 handling.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (
    $request,
    HttpNotFoundException $exception,
    bool $displayErrorDetails
) {
    $response = new Response();
    $response->getBody()->write("<h3>Page not found. Please check the URL.");
    $response->getBody()->write("\n <h4><a href='/Final-Project-Waltenberg/public' style='text-decoration: none'>Return to Home page?");
    return $response->withStatus(404);
});