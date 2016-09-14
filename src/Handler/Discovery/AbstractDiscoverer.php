<?php

namespace LastCall\Crawler\Handler\Discovery;

use LastCall\Crawler\Event\CrawlerResponseEvent;
use LastCall\Crawler\Uri\NormalizerInterface;
use LastCall\Crawler\Uri\Normalizations;
use LastCall\Crawler\CrawlerEvents;
use LastCall\Crawler\Event\CrawlerUrisDiscoveredEvent;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractDiscoverer
{
    protected $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    protected function processUris(CrawlerResponseEvent $event, EventDispatcherInterface $dispatcher, array $urls)
    {
        $resolve = Normalizations::resolve($event->getRequest()->getUri());

        $uris = [];
        foreach ($urls as $url) {
            $uri = new Uri($url);
            $uri = $resolve($uri);
            $uri = $this->normalizer->normalize($uri);
            $uris[(string) $uri] = $uri;
        }
        $uris = array_values($uris);

        $discoveryEvent = new CrawlerUrisDiscoveredEvent($event->getRequest(), $event->getResponse(), $uris);
        $dispatcher->dispatch(CrawlerEvents::URIS_DISCOVERED, $discoveryEvent);
        foreach ($discoveryEvent->getAdditionalRequests() as $request) {
            $event->addAdditionalRequest($request);
        }
    }
}
