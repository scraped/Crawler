<?php


namespace LastCall\Crawler\Handler\Discovery;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use LastCall\Crawler\Common\RedirectDetectionTrait;
use LastCall\Crawler\CrawlerEvents;
use LastCall\Crawler\Event\CrawlerResponseEvent;
use LastCall\Crawler\Url\TraceableUri;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Retries the original, denormalized form of a URL that results in
 * either failure or a redirect back to the original form.
 */
class DenormalizedUrlDiscoverer implements EventSubscriberInterface
{
    use RedirectDetectionTrait;

    public static function getSubscribedEvents()
    {
        return array(
            CrawlerEvents::SUCCESS => 'onSuccess',
            CrawlerEvents::FAILURE => 'onFailure',
        );
    }

    /**
     * Check whether the request has previous forms that can be
     * tried.
     *
     * @see \LastCall\Crawler\Url\TraceableUri
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    private function hasPreviousForms(RequestInterface $request)
    {
        $uri = $request->getUri();

        return $uri instanceof TraceableUri && $uri->getPrevious();
    }

    /**
     * Act on redirect of a response.
     *
     * @param \LastCall\Crawler\Event\CrawlerResponseEvent $event
     */
    public function onSuccess(CrawlerResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($this->isRedirectResponse($response) && $this->hasPreviousForms($event->getRequest())) {
            $location = $response->getHeaderLine('Location');

            $request = $event->getRequest();
            $location = (string)Uri::resolve($request->getUri(), $location);

            $uri = $event->getRequest()->getUri();
            while ($uri = $uri->getPrevious()) {
                if ($location === (string)$uri) {
                    $newRequest = new Request('GET', $uri);
                    $event->addAdditionalRequest($newRequest);
                }
            }
        }
    }

    /**
     * Act on request failure.
     *
     * @param \LastCall\Crawler\Event\CrawlerResponseEvent $event
     */
    public function onFailure(CrawlerResponseEvent $event)
    {
        if ($this->hasPreviousForms($event->getRequest())) {
            $previousUri = $event->getRequest()->getUri()->getPrevious();
            $newRequest = new Request('GET', $previousUri);
            $event->addAdditionalRequest($newRequest);
        }
    }

}