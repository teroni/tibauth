<?php

/**
 * Part of the Data Grid package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Data Grid
 * @version    3.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\DataGrid\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Cartalyst\DataGrid\RequestProviders\Provider;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    public function testProviderSetters()
    {
        $provider = $this->getProvider();

        $provider->setDefaultMethod('multi');
        $provider->setDefaultThreshold(200);
        $provider->setDefaultThrottle(200);

        $provider->getRequest()->shouldReceive('get')->times(3);

        $this->assertEquals('multi', $provider->getMethod());
        $this->assertEquals(200, $provider->getThreshold());
        $this->assertEquals(200, $provider->getThrottle());
    }

    public function testGettingRequest()
    {
        $provider = $this->getProvider();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');

        $this->assertEquals($request, $provider->getRequest());
    }

    public function testGettingFilters()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('filters', array())->once()->andReturn(array('foo'));
        $this->assertEquals(array('foo'), $provider->getFilters());
    }

    public function testGettingSort()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('sort')->once()->andReturn('foo');
        $this->assertEquals('foo', $provider->getSort());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('sort')->once();
        $this->assertNull($provider->getSort());
    }

    public function testGettingDirection()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('direction')->once()->andReturn('desc');
        $this->assertEquals('desc', $provider->getDirection());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('direction')->once()->andReturn('foo');
        $this->assertNull($provider->getDirection());
    }

    public function testGettingPage()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('page', 1)->once()->andReturn('4');
        $this->assertSame(4, $provider->getPage());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('page', 1)->once()->andReturn(0);
        $this->assertSame(1, $provider->getPage());
    }

    public function testGettingMethod()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('method')->once()->andReturn('single');
        $this->assertSame('single', $provider->getMethod());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('method')->once()->andReturn('group');
        $this->assertSame('group', $provider->getMethod());
    }

    public function testGettingThreshold()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('threshold')->once()->andReturn('4');
        $this->assertSame(4, $provider->getThreshold());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('threshold')->once()->andReturn(0);
        $this->assertSame(100, $provider->getThreshold());
    }

    public function testGettingThrottle()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('throttle')->once()->andReturn('4');
        $this->assertSame(4, $provider->getThrottle());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('throttle')->once()->andReturn(0);
        $this->assertSame(100, $provider->getThrottle());
    }

    public function testGettingDownload()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('download')->once()->andReturn('pdf');
        $this->assertSame('pdf', $provider->getDownload());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('download')->once()->andReturn('json');
        $this->assertSame('json', $provider->getDownload());
    }

    public function testGettingMaxResults()
    {
        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('max_results')->once()->andReturn(1);
        $this->assertSame(1, $provider->getMaxResults());

        $provider = $this->getProvider();
        $provider->getRequest()->shouldReceive('get')->with('max_results')->once()->andReturn(2);
        $this->assertSame(2, $provider->getMaxResults());
    }

    public function testDownloadJson()
    {
        $provider = $this->getProvider();

        $response = $provider->getResponse();

        $response->shouldReceive('create')->once()->andReturn($response);

        $provider->downloadJson(array(array('foo' => 'bar')));
    }

    public function testDownloadCsv()
    {
        $provider = $this->getProvider();

        $response = $provider->getResponse();

        $response->shouldReceive('create')->times(3)->andReturn($response);

        $provider->downloadCsv(array());

        $provider->downloadCsv(array(array('foo' => 'bar')));

        $provider->downloadCsv(array(array('foo' => 'bar')), ',', 'data-grid', function ($data) {
            return 1;
        });
    }

    public function testDownloadIlluminatePdf()
    {
        $provider = $this->getProvider();

        $view   = $provider->getView();
        $dompdf = $provider->getDompdf();

        $view->shouldReceive('make')->once();

        $dompdf->shouldReceive('load_html')->once();
        $dompdf->shouldReceive('render')->once();
        $dompdf->shouldReceive('stream')->once();

        $provider->downloadPdf(array(array('foo' => 'bar')), 'cartalyst/data-gird::pdf');
    }

    public function testDownloadNativePdf()
    {
        $provider = $this->getProvider();

        $view   = $provider->getView();
        $dompdf = $provider->getDompdf();

        $view->shouldReceive('make')->once();

        $dompdf->shouldReceive('load_html')->once();
        $dompdf->shouldReceive('render')->once();
        $dompdf->shouldReceive('stream')->once();

        $provider->downloadPdf(array(array('foo' => 'bar')), 'cartalyst/data-gird::pdf');
    }

    /**
     * @expectedException Exception
     */
    public function testDownloadPdfException()
    {
        $provider = new Provider(
            m::mock('Symfony\Component\HttpFoundation\Request'),
            m::mock('Symfony\Component\HttpFoundation\Response'),
            null,
            m::mock('NativeEnvironment')
        );

        $provider->downloadPdf(array(array('foo' => 'bar')), 'cartalyst/data-gird::pdf');
    }

    protected function getProvider()
    {
        return new Provider(
            m::mock('Symfony\Component\HttpFoundation\Request'),
            m::mock('Symfony\Component\HttpFoundation\Response'),
            m::mock('DOMPDFMOCK'),
            m::mock('NativeEnvironment')
        );
    }
}
