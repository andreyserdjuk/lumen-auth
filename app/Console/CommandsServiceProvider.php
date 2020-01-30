<?php

namespace App\Console;

use App\Console\Commands\SymfonyLumenCommandBridge;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

class CommandsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $dm = $this->app[ObjectManager::class];

        $helperSet = new HelperSet(
            [
                'dm' => new DocumentManagerHelper($dm),
            ]
        );

        $application = new Application();
        $application->setHelperSet($helperSet);
        $symfonyCommand = new CreateCommand();
        $application->add($symfonyCommand);

        $lumenCommand = new SymfonyLumenCommandBridge($symfonyCommand);

        $this->app->singleton('command.odm.schema.create', function ($app) use ($lumenCommand) {
            return $lumenCommand;
        });

        $this->commands([
            'command.odm.schema.create',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.odm.schema.create',
        ];
    }
}
