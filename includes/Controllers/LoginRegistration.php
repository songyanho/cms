<?php

if (!defined('IN_CORE_SYSTEM')){die('INVALID DIRECT ACCESS');}

$app->group('/guest', function(){
    
    /* Deprecated */
    $this->get('/new-admin-account', function ($request, $response, $args){
        die();
        $user = new KeyDecisionMaker();
        $user->setUsername('dm1');
        $user->setPassword(Core::createHashing('dm1'));
        $user->setTel('84092272');
        $user->setEmail('hoso0003@e.ntu.edu.sg');
        $user->save();
        return $response;
    })->setName(CMS::ROLE_GUEST.'#guest@account.create');
    
    $this->map(['get', 'post'], '/login', function ($request, $response, $args) {
        $twigVar = ['title' => 'Login', 'error' => false];
        if($request->isPost()){
            $params = $request->getParsedBody();
            $user = null;
            switch($params['domain']){
                case 'CallOperator':
                    $user = CallOperatorQuery::create()->findOneByUsername($params['username']);
                    break;
                case 'Agency':
                    $user = AgencyQuery::create()->findOneByUsername($params['username']);
                    break;
                case 'KeyDecisionMaker':
                    $user = KeyDecisionMakerQuery::create()->findOneByUsername($params['username']);
                    break;
                case 'Minister':
                    $user = MinisterQuery::create()->findOneByUsername($params['username']);
                    break;
                default:
                    $response = $response->withStatus(302)->withHeader('Location', $this->router->pathFor(CMS::ROLE_GUEST.'#guest@login.login'));
                    return $response;
                    break;
            }
            if($user != false){
                if(Core::verifyHashing($params['password'], $user->getPassword())){
                    Core::loginUser($user);
                    $response = $response->withStatus(302)->withHeader('Location', $this->router->pathFor(CMS::ROLE_GUEST.'#public@redirect'));
                    return $response;
                }
            }
            $twigVar['error'] = true;
        }
        return $this->view->render($response, 'LoginRegistration/login.html', $twigVar);
    })->setName(CMS::ROLE_GUEST.'#guest@login.login');
    
    $this->get('/logout', function ($request, $response, $args){
        if($user != false){
            $currentSession = LoginSessionQuery::create()->filterByUserId($this->cms->getUser()->getId())
                                                         ->filterByUserType(get_class($this->cms->getUser()))
                                                         ->filterByDisabled(false)
                                                         ->findOne();
            if($currentSession != false){
                $currentSession->setDisabled(true);
                $currentSession->save();
            }
        }
        session_destroy();
        $response = $response->withStatus(302)->withHeader('Location', $this->router->pathFor(CMS::ROLE_GUEST.'#public@redirect'));
        return $response;
    })->setName(CMS::ROLE_GUEST.'#public@login.logout');
});