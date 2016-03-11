<?php

if (!defined('IN_CORE_SYSTEM')){die('INVALID DIRECT ACCESS');}

$app->group('/map', function(){
    $this->get('/view', function ($request, $response, $args){
        return $this->view->render($response, 'Map/map.html');
    })->setName(CMS::ROLE_KEY_DECISION_MAKER.'&'.CMS::ROLE_MINISTER.'#map@view');
});