<?php

namespace LastCall\Crawler\Handler\Discovery;

use LastCall\Crawler\CrawlerEvents;
use LastCall\Crawler\Event\CrawlerHtmlResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImageDiscoverer extends AbstractDiscoverer implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CrawlerEvents::SUCCESS_HTML => 'onHtmlResponse',
            CrawlerEvents::FAILURE_HTML => 'onHtmlResponse',
        ];
    }

    public function onHtmlResponse(CrawlerHtmlResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $crawler = $event->getDomCrawler();
        $nodes = $crawler->filterXPath('descendant-or-self::img[@src]');
        $urls = $nodes->extract('src');
        $this->processUris($event, $dispatcher, $urls, 'image');
    }
}
