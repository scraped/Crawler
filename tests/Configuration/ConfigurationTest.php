<?php

namespace LastCall\Crawler\Test\Configuration;

use GuzzleHttp\Client;
use LastCall\Crawler\Configuration\Configuration;
use LastCall\Crawler\Queue\RequestQueueInterface;
use LastCall\Crawler\Url\URLHandler;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testBaseUrl()
    {
        $config = new Configuration();
        $this->assertNull($config->getBaseUrl());
        $config->setBaseUrl('http://google.com');
        $this->assertEquals('http://google.com', $config->getBaseUrl());
        $config = new Configuration('http://google.com');
        $this->assertEquals('http://google.com', $config->getBaseUrl());
    }

    public function testClient()
    {
        $config = new Configuration();
        $this->assertInstanceOf('GuzzleHttp\Client', $config->getClient());
        $client = new Client();
        $config->setClient($client);
        $this->assertSame($client, $config->getClient());
    }

    public function testUrlHandler()
    {
        $config = new Configuration();
        $this->assertInstanceOf(URLHandler::class, $config->getUrlHandler());
        $handler = $this->prophesize(URLHandler::class)->reveal();
        $config->setUrlHandler($handler);
        $this->assertSame($handler, $config->getUrlHandler());
    }

    public function testQueue()
    {
        $config = new Configuration();
        $this->assertInstanceOf(RequestQueueInterface::class,
            $config->getQueue());
        $queue = $this->prophesize(RequestQueueInterface::class)->reveal();
        $config->setQueue($queue);
        $this->assertSame($queue, $config->getQueue());
    }

    public function testSubscribers()
    {
        $config = new Configuration('http://google.com');
        $this->assertEquals([], $config->getSubscribers());

        $subscriber = $this->prophesize(EventSubscriberInterface::class)
            ->reveal();
        $config->addSubscriber($subscriber);
        $this->assertSame([$subscriber], $config->getSubscribers());
    }

    public function testListeners()
    {
        $config = new Configuration('http://google.com');
        $this->assertEquals([], $config->getListeners());

        $listener = function () {
        };
        $config->addListener('foo', $listener, 10);
        $this->assertEquals(['foo' => [[$listener, 10]]],
            $config->getListeners());
    }

//
//    public function testSetup() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::SETUP)->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onSetup();
//    }
//
//    public function testTeardown() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::TEARDOWN)->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onTeardown();
//    }
//
//    public function testOnRequestSending() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::SENDING, Argument::type(CrawlerEvent::class))->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onRequestSending(new Request('GET', 'http://google.com'));
//    }
//
//    public function testOnRequestSuccess() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::SUCCESS, Argument::type(CrawlerResponseEvent::class))->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onRequestSuccess(new Request('GET', 'http://google.com'), new Response());
//    }
//
//    public function testOnRequestFailure() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::FAILURE, Argument::type(CrawlerResponseEvent::class))->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onRequestFailure(new Request('GET', 'http://google.com'), new Response());
//    }
//
//    public function testOnRequestException() {
//        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
//        $dispatcher->dispatch(CrawlerEvents::EXCEPTION, Argument::type(CrawlerExceptionEvent::class))->shouldBeCalledTimes(1);
//        $config = new Configuration(NULL, $dispatcher->reveal());
//        $config->onRequestException(new Request('GET', 'http://google.com'),
//            new \Exception('foo'), new Response());
//    }
}