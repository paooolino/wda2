<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set("Europe/Rome");
setlocale(LC_TIME, "it_IT");

// set container
$container = new \DI\Container();
Slim\Factory\AppFactory::setContainer($container);

// Instantiate the Slim App
$app = Slim\Factory\AppFactory::create();
$app->setBasePath('/wda2/examples/authenticationV4');

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Set up dependencies
require __DIR__ . '/app/dependencies.php';

// App middleware
require __DIR__ . '/app/middleware.php';

// App routes
require __DIR__ . '/app/routes.php';

$app->run();
