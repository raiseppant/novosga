<?php

namespace AppBundle\Service;

use Symfony\Component\Finder\Finder;


class ModuleService
{

    /**
     *
     * @return array
     */
    public function getModules()
    {
        $configuration = ConfigurationService::get();

        if (!isset($configuration['modules'])) {
            $configuration['modules'] = $this->discover();
            ConfigurationService::set($configuration);
        }

        $modules = $configuration['modules'];

        return $modules;
    }

    public function discover()
    {
        $searchPath = realpath(__DIR__ . '/../../../modules');
        $finder     = new Finder();
        $finder->files()
               ->in($searchPath)
               ->name('*Bundle.php');

        $modules = [];

        foreach ($finder as $file) {
            $path       = substr($file->getRealpath(), strlen($searchPath) + 1, -4);
            $parts      = explode('/', $path);
            $class      = array_pop($parts);
            $namespace  = implode('\\', $parts);
            $class      = '\\' . $namespace.'\\'.$class;
            if (class_exists($class)) {
                $modules[]  = [
                    'active' => true,
                    'class' => $class
                ];
            }
        }

        return $modules;
    }

}