<?php

//ini_set('display_startup_errors',1);
//ini_set('display_errors',1);
//error_reporting(-1);

define('IN_CORE_SYSTEM', true);

//error_reporting(E_ERROR ^ E_WARNING);
// create session
session_cache_limiter(false);
session_start();

// Loads all dependencies namely Slim Framework
require_once 'vendor/autoload.php';

// Slim Initialization
require_once 'Init.php';

// Loads all Middleware, Models, Controller
require_once 'Autoload.php';

// Middleware Injection, Dependencies Injection
require_once 'Injection.php';

// Propel Setup
require_once 'generated-conf/config.php';

$app->get('/', function ($request, $response, $args) { 
    return $this->view->render($response, 'LandingPage/landing.html', []); 
})->setName(CMS::ROLE_GUEST.'#public@landingPage');

$app->get('/redirect', function ($request, $response, $args){
    $user = $this->cms->getUser();
    $role = CMS::getUserRole($user);
    $path = '';
    switch($role){
        case CMS::ROLE_CALL_OPERATOR:
            $path = CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@list';
            break;
        case CMS::ROLE_AGENCY:
            $path = CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@list';
            break;
        case CMS::ROLE_KEY_DECISION_MAKER:
            $path = CMS::ROLE_KEY_DECISION_MAKER.'&'.CMS::ROLE_MINISTER.'#map@view';
            break;
        case CMS::ROLE_MINISTER:
            $path = CMS::ROLE_KEY_DECISION_MAKER.'&'.CMS::ROLE_MINISTER.'#map@view';
            break;
        default:
            $path = CMS::ROLE_GUEST.'#public@landingPage';
            break;
    }
    $response = $response->withStatus(302)->withHeader('Location', $this->router->pathFor($path));
    return $response;
})->setName(CMS::ROLE_GUEST.'#public@redirect');

// Starts Slim Framework
$app->run();