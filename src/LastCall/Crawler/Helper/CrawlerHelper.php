<?php

namespace LastCall\Crawler\Helper;


use LastCall\Crawler\Configuration\ConfigurationInterface;
use LastCall\Crawler\Crawler;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CrawlerHelper extends Helper
{

    public function getName() {
        return 'crawler';
    }

    public function getCrawler(ConfigurationInterface $config, $profile = FALSE) {
        if($profile && $this->getHelperSet()->has('profiler')) {
            $profiler = $this->getHelperSet()->get('profiler');
            $dispatcher = $profiler->getTraceableDispatcher(new EventDispatcher());
            return new Crawler($config, $dispatcher);
        }
        return new Crawler($config);
    }

    /**
     * @param $file
     *
     * @return \LastCall\Crawler\Configuration\ConfigurationInterface
     */
    public function getConfiguration($filename, OutputInterface $output) {
        if(!is_file($filename)) {
            throw new \InvalidArgumentException(sprintf('File does not exist: %s', $filename));
        }
        $configuration = require $filename;
        if(!$configuration) {
            throw new \RuntimeException('Configuration was not returned.');
        }
        if(!$configuration instanceof ConfigurationInterface) {
            throw new \RuntimeException(sprintf('Configuration must implement %s', ConfigurationInterface::class));
        }
        return $configuration;
    }
}