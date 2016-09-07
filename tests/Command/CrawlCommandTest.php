<?php

namespace LastCall\Crawler\Test;

use GuzzleHttp\Promise\FulfilledPromise;
use LastCall\Crawler\Configuration\ConfigurationInterface;
use LastCall\Crawler\Configuration\Loader\ConfigurationLoaderInterface;
use LastCall\Crawler\Crawler;
use LastCall\Crawler\Queue\RequestQueueInterface;
use LastCall\Crawler\Test\Resources\DummyCrawlCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CrawlCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testRunsCrawler()
    {
        $config = $this->prophesize(ConfigurationInterface::class);
        $queue = $this->prophesize(RequestQueueInterface::class);
        $config->getQueue()->willReturn($queue);
        $config->getListeners()->willReturn([]);
        $config->getSubscribers()->willReturn([]);

        $loader = $this->prophesize(ConfigurationLoaderInterface::class);
        $loader->loadFile('crawler.php')->willReturn($config);

        $crawler = $this->prophesize(Crawler::class);
        $crawler->start(5)
            ->willReturn(new FulfilledPromise('foo'))
            ->shouldBeCalled();

        $command = new DummyCrawlCommand();
        $command->setLoader($loader->reveal());
        $command->setCrawler($crawler->reveal());
        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}