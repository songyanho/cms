<?php

if (!defined('IN_CORE_SYSTEM')){die('INVALID DIRECT ACCESS');}

$app->group('/category', function(){
    $this->get('/list/json', function ($request, $response, $args){
        $categories = CategoryQuery::create()->find()->toJson();
        return $categories;
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#category@listjson');
    
    $this->get('/new', function($request, $response, $args){
        die();
        $a = ['Fire', 'Alien invasion', 'Virus'];
        foreach($a as $b){
            $c = new Category();
            $c->setName($b);
            $c->save();
        }
        die();
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#category@new');
});