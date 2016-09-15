<?php

namespace LastCall\Crawler\Configuration;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines a configuration for the crawler.
 */
interface ConfigurationInterface
{
    /**
     * Get the HTTP client to be used for this configuration.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient();

    /**
     * Get the queue to be used for this configuration.
     *
     * @return \LastCall\Crawler\Queue\RequestQueueInterface
     */
    public function getQueue();

    /**
     * Attach listeners to an event dispatcher.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function attachToDispatcher(EventDispatcherInterface $dispatcher);
}
