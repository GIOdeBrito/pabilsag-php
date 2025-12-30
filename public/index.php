<?php

define('ABSPATH', __DIR__);
date_default_timezone_set('America/Fortaleza');

require ABSPATH.'/../vendor/autoload.php';

use GioPHP\Core\Application as App;

$app = new App();

$app->loader()->setViewDirectory(ABSPATH."/src/Views");

$app->error()->useLogging();
$app->error()->setErrorCallback(function ($message)
{
	echo <<<HTML
		<h1>Uh-oh. There was an error!</h1>
		<p>{$message}</p>
	HTML;
});

// Import controller classes
require 'src/Controllers/Home.php';
require 'src/Controllers/FileController.php';
require 'src/Controllers/ApiController.php';
require 'src/Controllers/WebController.php';
require 'src/Controllers/MiddlewareController.php';
require 'src/Controllers/DIController.php';

// Controllers registered on router
$app->router()->addController(Home::class);
$app->router()->addController(FileController::class);
$app->router()->addController(ApiController::class);
$app->router()->addController(WebController::class);
$app->router()->addController(MiddlewareController::class);
$app->router()->addController(DIController::class);

// Component use and import
$app->components()->useComponents(true);
$app->components()->import(include ABSPATH.'/src/Components/ButtonIcon/button-icon.php');
$app->components()->import(include ABSPATH.'/src/Components/Header/header.php');

require 'src/Middlewares/JSONBody.php';

// Global middlewares
$app->addMiddleware(JSONBody::class);

// Register database for connection
$app->loader()->importConnectionMetadata(ABSPATH.'/src/Config/Connections.php');

// For dependency injection
require 'src/DependencyInjection/SessionManager.php';

// Bind service for injection
$app->container()->bind(SessionManager::class, fn($container) => new SessionManager(
	$container->make(GioPHP\Services\Logger::class)
));

$app->run();

?>