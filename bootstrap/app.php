<?php

use App\Console\CommandsServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\ObjectManager;
use MongoDB\Client;

if ( ! file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    throw new RuntimeException('Install dependencies to run this script.');
}
$loader = require($file);

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

$loader->add('Documents', __DIR__.'/../src/Documents');
$config = new Configuration();
$config->setProxyDir(__DIR__ . '/../storage/DoctrineODM/Proxies');
$config->setProxyNamespace('Proxies');
$config->setHydratorDir(__DIR__ . '/../storage/DoctrineODM/Hydrators');
$config->setHydratorNamespace('Hydrators');
spl_autoload_register($config->getProxyManagerConfiguration()->getProxyAutoloader());
$config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__.'/../src/Documents'));
$config->setDefaultDB(env('DB_DATABASE', 'lumen-auth'));
$conn = new Client(env('DB_CONNECTION'), [], [
    'typeMap' => DocumentManager::CLIENT_TYPEMAP,
]);
AnnotationRegistry::registerLoader('class_exists');
$dm = DocumentManager::create($conn, $config);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

// $app->withFacades();

// $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->instance(
    ObjectManager::class,
    $dm
);

$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->configure('mail');
$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);

$app->register(CommandsServiceProvider::class);

$app->withFacades();

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
