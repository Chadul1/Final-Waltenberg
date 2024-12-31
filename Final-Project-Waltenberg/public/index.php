<?php 

namespace App;

require_once('../logger/logger.php');
require '../vendor/autoload.php';
require '../public/functions.php';

use function Logger\initializeLogger;
use Logger\LoggerFactory;
use Slim\Factory\AppFactory;



//create routing app.
$app = AppFactory::create();
session_start();
require_once('Middleware.php');
require_once('routes.php');

//setup the logger
initializeLogger('text');
$logger = LoggerFactory::getInstance()->getLogger();

//run app.
$app->run();

