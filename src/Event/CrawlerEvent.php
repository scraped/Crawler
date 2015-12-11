<?php

namespace LastCall\Crawler\Event;

use LastCall\Crawler\CrawlerSession;
use LastCall\Crawler\Url\URLHandler;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;


class CrawlerEvent extends Event
{

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var \LastCall\Crawler\Url\URLHandler
     */
    private $urlHandler;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $discoveredRequests = [];

    public function __construct(
        RequestInterface $request,
        URLHandler $handler
    ) {
        $this->request = $request;
        $this->urlHandler = $handler;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getUrlHandler()
    {
        return $this->urlHandler;
    }

    public function addAdditionalRequest(RequestInterface $request)
    {
        $this->discoveredRequests[] = $request;
    }

    public function getAdditionalRequests()
    {
        return $this->discoveredRequests;
    }
}