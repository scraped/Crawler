<?php

namespace LastCall\Crawler\Queue;

use Psr\Http\Message\RequestInterface;

/**
 * In-memory request queue.
 */
class ArrayRequestQueue implements RequestQueueInterface
{
    private $incomplete = [];
    private $pending = [];
    private $complete = [];
    private $expires = [];

    public function push(RequestInterface $request)
    {
        $key = $this->getKey($request);
        if (!isset($this->incomplete[$key]) && !isset($this->pending[$key]) && !isset($this->complete[$key])) {
            $this->incomplete[$key] = $request;

            return true;
        }

        return false;
    }

    private function getKey(RequestInterface $request)
    {
        return $request->getMethod().$request->getUri();
    }

    public function pushMultiple(array $requests)
    {
        $return = array_fill_keys(array_keys($requests), false);
        $keys = array_unique(array_map([$this, 'getKey'], $requests));
        foreach ($keys as $i => $key) {
            if (!isset($this->incomplete[$key]) && !isset($this->pending[$key]) && !isset($this->complete[$key])) {
                $this->incomplete[$key] = $requests[$i];
                $return[$i] = true;
            }
        }

        return $return;
    }

    public function pop($leaseTime = 30)
    {
        $this->expire();
        if (!empty($this->incomplete)) {
            $request = array_shift($this->incomplete);
            $key = $this->getKey($request);
            $this->expires[$key] = time() + $leaseTime;

            return $this->pending[$key] = $request;
        }

        return;
    }

    private function expire()
    {
        $time = time();
        $expiring = array_filter($this->expires,
            function ($expiration) use ($time) {
                return $expiration <= $time;
            });
        foreach ($expiring as $key => $expiration) {
            $this->releasePending($key);
        }
    }

    private function releasePending($key)
    {
        $this->incomplete[$key] = $this->pending[$key];
        unset($this->pending[$key], $this->expires[$key]);
    }

    public function complete(RequestInterface $request)
    {
        $this->expire();
        $key = $this->getKey($request);
        if (isset($this->pending[$key])) {
            $this->complete[$key] = $this->pending[$key];
            unset($this->pending[$key], $this->expires[$key]);

            return;
        }
        throw new \RuntimeException('This request is not managed by this queue');
    }

    public function release(RequestInterface $request)
    {
        $this->expire();
        $key = $this->getKey($request);
        if (isset($this->pending[$key])) {
            $this->releasePending($key);

            return;
        }
        throw new \RuntimeException('This request is not managed by this queue');
    }

    public function count($status = self::FREE)
    {
        $this->expire();
        switch ($status) {
            case self::FREE:
                return count($this->incomplete);
            case self::PENDING:
                return count($this->pending);
            case self::COMPLETE:
                return count($this->complete);
        }
        throw new \RuntimeException(sprintf('Unexpected status %s',
            (string) $status));
    }
}
