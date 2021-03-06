<?php

use \Slim\App AS Slim;
// Slim Framework Initialization
$app = new Slim([
    'settings'          => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ],
    'twig'              => [
        'maintitle'         => 'Crisis Management System',
        'description'   => '',
        'author'        => 'Songyan Ho',
        'baseHref'      => 'http://cms.local/', // 'http://172.21.148.164/'
    ],
    'view'              => function($c){
        $view = new \Slim\Views\Twig('templates', [
            'cache' => false // './cache'
        ]);
        // Instantiate and add Slim specific extension
        $view->addExtension(new \Slim\Views\TwigExtension(
            $c['router'],
            $c['request']->getUri()
        ));
        
        foreach ($c['twig'] as $name => $value) {
            $view->getEnvironment()->addGlobal($name, $value);
        }
        return $view;
    }
]);