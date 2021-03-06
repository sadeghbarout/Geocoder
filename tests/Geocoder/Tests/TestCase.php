<?php

namespace Geocoder\Tests;

use Geocoder\Model\AddressFactory;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\CurlHttpAdapter;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param  null|object          $expects
     * @return HttpAdapterInterface
     */
    protected function getMockAdapter($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $response = $this->getMock('Psr\Http\Message\MessageInterface');
        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(
                $this->getMock('Psr\Http\Message\StreamableInterface')
            ));

        $adapter = $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $adapter
            ->expects($expects)
            ->method('get')
            ->will($this->returnValue($response));

        return $adapter;
    }

    /**
     * @param $returnValue
     * @return HttpAdapterInterface
     */
    protected function getMockAdapterReturns($returnValue)
    {
        $body = $this->getMock('Psr\Http\Message\StreamableInterface');
        $body
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue((string) $returnValue));

        $response = $this->getMock('Psr\Http\Message\MessageInterface');
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($body));

        $adapter = $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $adapter
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($response));

        return $adapter;
    }

    /**
     * Because I was bored to fix the test suite because of
     * a change in a third-party API...
     *
     * @return HttpAdapterInterface
     */
    protected function getAdapter()
    {
        return new CachedResponseAdapter(new CurlHttpAdapter(), $this->useCache());
    }

    /**
     * @return boolean
     */
    protected function useCache()
    {
        return isset($_SERVER['USE_CACHED_RESPONSES']) && true === $_SERVER['USE_CACHED_RESPONSES'];
    }

    protected function createAddress(array $data)
    {
        $addresses = (new AddressFactory())->createFromArray([ $data ]);

        return 0 === count($addresses) ? null : $addresses[0];
    }

    protected function createEmptyAddress()
    {
        return $this->createAddress([]);
    }
}
