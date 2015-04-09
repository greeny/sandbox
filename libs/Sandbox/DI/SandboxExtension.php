<?php
/**
 * @author Tomáš Blatný
 */

namespace Sandbox\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;

class SandboxExtension extends CompilerExtension
{
    public $defaults = [
        'leanmapper' => [
            'mapper' => 'LeanMapper\DefaultMapper',
            'entityFactory' => 'LeanMapper\DefaultEntityFactory',
            'connection' => 'LeanMapper\Connection',
            'host' => 'localhost',
            'driver' => 'mysqli',
            'username' => NULL,
            'password' => NULL,
            'database' => NULL,
            'lazy' => TRUE,
        ]
    ];

    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);
        $config = $config['leanmapper'];
        $builder = $this->getContainerBuilder();
        if (!isset($config['username']) && isset($config['user'])) {
            $config['username'] = $config['user'];
        }
        unset($config['user']);

        $useProfiler = isset($config['profiler'])
            ? $config['profiler']
            : class_exists('Tracy\Debugger') && $builder->parameters['debugMode'];
        unset($config['profiler']);

        $connection = $this->configureConnection($builder, $config);

        $builder->addDefinition($this->prefix('entityFactory'))
            ->setClass($config['entityFactory']);

        $builder->addDefinition($this->prefix('mapper'))
            ->setClass($config['mapper']);

        if ($useProfiler) {
            $panel = $builder->addDefinition($this->prefix('panel'))
                ->setClass('Dibi\Bridges\Tracy\Panel');
            $connection->addSetup([$panel, 'register'], [$connection]);
        }
    }

    protected function configureConnection(ContainerBuilder $builder, array $config)
    {
        if (!isset($config['connection']) || !is_string($config['connection'])) {
            throw new \RuntimeException('Connection class definition is missing, or not (string).');
        }
        return $builder->addDefinition($this->prefix('connection'))
            ->setClass($config['connection'], [
                [
                    'host' => $config['host'],
                    'driver' => $config['driver'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'database' => $config['database'],
                    'lazy' => (bool) $config['lazy'],
                ],
            ]);
    }
}
