<?php

class Authentication{
    private $c;
    public function __construct($class, $container){
        $this->c = $container;
    }
    public function __invoke($request, $response, $next)
    {
//        $response = $next($request, $response);
        /*
        UserRole
        
        
        */
        $secured = false;
        if(method_exists($request->getAttribute('route'), 'getName')){
            $routeName = $request->getAttribute('route')->getName();
        }else{
            $response = $response->withStatus(404);
            return $response;
        }
        $route = new AuthRoute($routeName);
        $this->c->cms->setRoute($route);
        $thisUser = Core::checkLoggedUser();
        $this->c->cms->setUser($thisUser);
        
        if($route->getGroup() === 'public'){
            $response = $next($request, $response);
            return $response;
        }
        
        if(in_array(CMS::ROLE_GUEST, $route->getRoles()) && 
           $thisUser !== false && 
           $route->getAction() !== 'logout'){
            // Change Name
            $response = $response->withStatus(301)->withHeader("Location", $this->c->router->pathFor(CMS::ROLE_GUEST.'#public@redirect'));
            $response = $next($request, $response);
            return $response;
        }
        // Must be a logged user
        if($thisUser == false){
            if($route->getGroup() === 'guest'){
                $response = $next($request, $response);
                return $response;
            }
            $response = $response->withStatus(301)->withHeader("Location", $this->c->router->pathFor(CMS::ROLE_GUEST.'#guest@login.login'));
            $response = $next($request, $response);
            return $response;
        }
        // Must have higher clearance
        else if(!$route->isAllowedByUser($thisUser)){
            // Invalid access
            $response = $response->withStatus(400);
//            $response->write('Invalid access');
            $response = $next($request, $response);
            return $response;
        }
        $response = $next($request, $response);
        return $response;
    }
}