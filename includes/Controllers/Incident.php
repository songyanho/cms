<?php

use Propel\Runtime\Formatter\ObjectFormatter;

if (!defined('IN_CORE_SYSTEM')){die('INVALID DIRECT ACCESS');}

$app->group('/incident', function(){
    $this->get('/list', function ($request, $response, $args){
        $incidents = IncidentQuery::create()->filterByActive(true)->orderByCreatedAt('desc')->limit(100)->find();
        $filter = new Twig_SimpleFilter("numberOfReporter", function ($caseId) {
            $incident = IncidentQuery::create()->findPK($caseId);
            return $incident == null ? '-' : $incident->getTitle();
        });
        $twig = $this->view->getEnvironment();
        $twig->addFilter($filter);
        return $this->view->render($response, 'Incident/list.html', [
            'title' => 'List incidents',
            'incidents' => $incidents
        ]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@list');
    
    $this->get('/new', function ($request, $response, $args){
        return $this->view->render($response, 'Incident/create.html', [
            'title' => 'New incident',
        ]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@new');
    
    $this->post('/new-incident', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["title"]))
            Core::apiErrorJson('Telephone number is required', 1700);
        if(!Core::checkNullValue($params["address"]))
            Core::apiErrorJson('Location is required', 1400);
        if(!Core::checkNullValue($params["lat"]))
            Core::apiErrorJson('System error: Latitude is required', 1500);
        if(!Core::checkNullValue($params["lng"]))
            Core::apiErrorJson('System error: Longitude is required', 1600);

        $newIncident = new Incident();
        $newIncident->setTitle($params["title"]);
        $newIncident->setLocation(strtoupper($params["address"]));
        $newIncident->setLatitude($params["lat"]);
        $newIncident->setLongitude($params["lng"]);
        $newIncident->setActive(true);
        $newIncident->save();

        Core::successApiJson('New Incident is saved', false, ['id'=> $newIncident->getId()]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@new-incident');
    
    $this->post('/find-incidents', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["lat"]))
            Core::apiErrorJson('Latitude ID is required', 1000);
        if(!Core::checkNullValue($params["lng"]))
            Core::apiErrorJson('Longitude is required', 1100);
        
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\IncidentTableMap::DATABASE_NAME);
        $query = "SELECT *, (6371 * acos ( cos ( radians(".$params['lat'].") ) * cos( radians( incident.latitude ) ) * cos( radians( incident.longitude ) - radians(".$params['lng'].") ) + sin ( radians(".$params['lat'].") ) * sin( radians( incident.latitude ) ) ) ) AS distance FROM `incident` HAVING distance < 2 ORDER BY distance LIMIT 0 , 20;";
        $stmt = $con->prepare($query);
        $res = $stmt->execute();
        $formatter = new ObjectFormatter();
        $formatter->setClass('Incident'); //full qualified class name
        $incidents = $formatter->format($con->getDataFetcher($stmt));
        Core::successApiJson('List of incidents', false, ['incidents'=>$incidents->count() ? $incidents->toJson() : '{}']);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@find-incidents');
    
    $this->post('/find-reporters', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["tel"]))
            Core::apiErrorJson('Telephone is required', 1200);
        
        $reporters = ReporterQuery::create()->findByTel($params["tel"]);
        
        Core::successApiJson('List of reporters', false, ['reporters'=>$reporters->count() ? $reporters->toJson() : '{}']);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@find-reporters');
    
    $this->post('/new-reporter', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["id"]))
            Core::apiErrorJson('Incident ID is required', 1000);
        if(!Core::checkNullValue($params["reporterId"]))
            Core::apiErrorJson('Reporter ID is required', 1000);
        if($params["reporterId"] === 0){
            if(!Core::checkNullValue($params["name"]))
                Core::apiErrorJson('Name is required', 1100);
            if(!Core::checkNullValue($params["tel"]))
                Core::apiErrorJson('Telephone number is required', 1200);
        }
        if(!Core::checkNullValue($params["description"]))
            Core::apiErrorJson('Description is required', 1300);
        $existingReporter = null;
        if($params["reporterId"] > 0)
            $existingReporter = ReporterQuery::create()->findPK($params["reporterId"]);
        else{
            $existingReporter = new Reporter();
            $existingReporter->setName(strtoupper($params["name"]));
            $existingReporter->setTel($params["tel"]);
            $existingReporter->save();
        }

        $newIncident = IncidentQuery::create()->findPK($params["id"]);
        $newIncident->addReporter($existingReporter);
        $newIncident->save();

        $newIncidentReporter = IncidentReporterQuery::create()->filterByIncident($newIncident)->filterByReporter($existingReporter)->findOne();
        $newIncidentReporter->setDescription($params["description"]);
        $newIncidentReporter->save();

        Core::successApiJson('New Reporter is saved', false, ['id'=> $newIncident->getId(), 'reporterId'=>$existingReporter->getId()]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@new-reporter');
    
    $this->post('/new-categories-and-resource', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["id"]))
            Core::apiErrorJson('Incident ID is required', 1000);
        if(!Core::checkNullValue($params["reporterid"]))
            Core::apiErrorJson('Reporter ID is required', 1000);
        $categories = explode(',', $params["categories"]);
        if($categories < 1)
            Core::apiErrorJson('At least one category is required', 1000);

        $matchedCategories = [];
        foreach($categories as $v){
            $cat = CategoryQuery::create()->orderById()->findOneByName(ucfirst($v));
            if($cat != null)
                $matchedCategories[] = $cat;
            else{
                $newCategories = new Category();
                $newCategories->setName(ucfirst($v));
                $newCategories->save();
                $matchedCategories[] = $newCategories;
            }
        }
        
        $newIncident = IncidentQuery::create()->findPK($params["id"]);
        $newIncident->setActive(true);
        
        $previousCategories = $newIncident->getCategories();
        if($previousCategories->count()>1){
            foreach($previousCategories as $t){
                $newIncident->removeCategory($t);
            }
        }elseif($previousCategories->count()==1){
            $newIncident->removeCategory($previousCategories);
        }
        foreach($matchedCategories as $v){
            $newIncident->addCategory($v);
        }
        
        $reporter = ReporterQuery::create()->findPK($params["reporterid"]);
        
        $resources = json_decode($params["resources"], true);
        foreach($resources as $v){
            if($v["selected"]){
                $resource = ResourceQuery::create()->findPK($v["id"]);
                $newIncident->addResource($resource);
                $newRecord = new IncidentResourceRecord();
                $newRecord->setIncident($newIncident);
                $newRecord->setResource($resource);
                $newRecord->setReporter($reporter);
                $newRecord->save();
            }
        }
        
        $newIncident->save();

        Core::successApiJson('New Category and Resource required is saved', false, ['id'=> $newIncident->getId()]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@new-categories-and-resource');
    
    $this->post('/get-categories-and-resources', function ($request, $response, $args){
        $params = $request->getParsedBody();
        if(!Core::checkNullValue($params["id"]))
            Core::apiErrorJson('Incident ID is required', 1000);
        
        $incident = IncidentQuery::create()->findPK($params["id"]);
        if($incident == null)
            Core::apiErrorJson('Incident ID is invalid', 1000, $this->router->pathFor(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@list'));
        $categories = $incident->getCategories();
        
        $availableResources = ResourceQuery::create()->find();
        
        $resources = $incident->getResources();
        $records = [];
        if($resources->count()>0){
            foreach($resources as $resource){
                $records[$resource->getId()] = [];
                $record_s = IncidentResourceRecordQuery::create()->filterByIncident($incident)->filterByResource($resource)->orderByCreatedAt(Criteria::DESC)->find();
                if($record_s->count()>1){
                    foreach($record_s as $s)
                        $records[$resource->getId()][] = $s->toJson();
                }elseif($record_s->count()==1){
                    $records[$resource->getId()][] = $record_s->toJson();
                }
            }
        }
        
        Core::successApiJson('Categories are fetched', false, [
            'categories'=> $categories->count() ? $categories->toJson() : '{}',
            'resources'=>$availableResources->count() ? $availableResources->toJson() : '{}',
            'records'=>json_encode($records)
        ]);
    })->setName(CMS::ROLE_CALL_OPERATOR.'&'.CMS::ROLE_AGENCY.'#incident@get-categories');
    
//    $this->get('/view/{id}', function ($request, $response, $args){
//        
//    })->
});