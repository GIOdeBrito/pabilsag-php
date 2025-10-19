<?php

define('ABSPATH', __DIR__);
date_default_timezone_set('America/Fortaleza');

require __DIR__.'/../vendor/autoload.php';

use GioPHP\Core\GioPHPApp as App;

require 'src/Controllers/Home.php';
require 'src/Controllers/FileController.php';
require 'src/Controllers/ApiController.php';
require 'src/Controllers/WebController.php';
require 'src/Controllers/MiddlewareController.php';
require 'src/Controllers/DIController.php';

// For dependency injection
require 'src/DependencyInjection/SessionManager.php';

$app = new App();

$app->loader()->setViewDirectory(__DIR__."/src/Views");
$app->loader()->setConnectionString("sqlite:".__DIR__.'/database.db');
$app->error()->setErrorCallback(function ($message)
{
	echo "<h1>Uh-oh. There was an error!</h1>";
	echo "<p>{$message}</p>";
});

// Controllers registered on router
$app->router()->addController(Home::class);
$app->router()->addController(FileController::class);
$app->router()->addController(ApiController::class);
$app->router()->addController(WebController::class);
$app->router()->addController(MiddlewareController::class);
$app->router()->addController(DIController::class);

// Component use and import
$app->components()->useComponents(true);
$app->components()->import(include constant('ABSPATH').'/src/Components/ButtonIcon/button-icon.php');

// Dependency injection bind
$app->container()->bind(SessionManager::class, fn($container) => new SessionManager($container->make(GioPHP\Services\Logger::class)));

$app->run();

?>