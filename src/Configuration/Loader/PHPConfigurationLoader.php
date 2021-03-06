<?php

namespace LastCall\Crawler\Configuration\Loader;

use LastCall\Crawler\Configuration\ConfigurationInterface;

/**
 * A configuration loader that `require`'s a PHP file.
 */
class PHPConfigurationLoader implements ConfigurationLoaderInterface
{
    public function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('Configuration file %s does not exist.', $filename));
        }
        if ('php' !== pathinfo($filename, PATHINFO_EXTENSION)) {
            throw new \Exception(sprintf('Configuration file %s does not have a .php file extension.', $filename));
        }
        $configuration = require $filename;

        if (!$configuration || !$configuration instanceof ConfigurationInterface) {
            throw new \Exception(sprintf('Configuration must implement %s', ConfigurationInterface::class));
        }

        return $configuration;
    }
}
