<?php

if (!defined('IN_CORE_SYSTEM')){die('INVALID DIRECT ACCESS');}

// Middleware
require_once 'includes/Middlewares/Authentication.php';
require_once 'includes/Middlewares/TwigVariableInjectionMiddleware.php';

// Dependencies
require_once './includes/Dependencies/CMS.php';

// System Models
require_once 'includes/Models/Core.php';
require_once 'includes/Models/AuthRoute.php';

// Controller
require_once 'includes/Controllers/LoginRegistration.php';
require_once 'includes/Controllers/Map.php';
require_once 'includes/Controllers/Incident.php';
require_once 'includes/Controllers/Category.php';