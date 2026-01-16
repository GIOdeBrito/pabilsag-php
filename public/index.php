<?php

define('ABSPATH', __DIR__);
date_default_timezone_set('America/Fortaleza');

require ABSPATH.'/../src/Core/Autoloader.php';

use Pabilsag\Core\Application as App;

$app = new App();

$app->loader()->setViewDirectory(ABSPATH."/src/Views");

//$app->error()->useLogging();
$app->error()->setErrorCallback(function (string $message)
{
	echo <<<HTML
		<h1>Uh-oh, there was an error.</h1>
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

// Middlewares
require 'src/Middlewares/GetNuke.php';

// Controllers registered on router
$app->router()->addController(Home::class);
$app->router()->addController(FileController::class);
$app->router()->addController(ApiController::class);
$app->router()->addController(WebController::class);
$app->router()->addController(MiddlewareController::class);
$app->router()->addController(DIController::class);

// Add global middlewares
$app->middleware()->add(Pabilsag\Middlewares\JSONParse::class);
//$app->middleware()->add(GETNuke::class);

// Register database for connection
$app->loader()->importConnectionMetadata(ABSPATH.'/src/Config/Connections.php');

// For dependency injection
require 'src/DependencyInjection/SessionManager.php';

// Bind service for injection
$app->container()->bind(SessionManager::class, fn($container) => new SessionManager(
	$container->make(Pabilsag\Services\Logger::class)
));

$app->run();

?>