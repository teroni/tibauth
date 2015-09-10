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

namespace Cartalyst\DataGrid\RequestProviders;

use DOMPDF;
use Closure;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Cartalyst\DataGrid\View\NativeEnvironment;
use Symfony\Component\HttpFoundation\Response;

class Provider implements ProviderInterface
{
    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * The DOMPDF instance.
     *
     * @var \DOMPDF
     */
    protected $dompdf;

    /**
     * The view instance.
     *
     * @var mixed
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param  $request  \Symfony\Component\HttpFoundation\Request
     * @param  $response  \Symfony\Component\HttpFoundation\Response
     * @param  $dompdf  \DOMPDF
     * @param  $view  \Illuminate\View\Environment
     * @return void
     */
    public function __construct(Request $request = null, Response $response = null, $dompdf = null, $view = null)
    {
        $this->request = $request ?: Request::createFromGlobals();

        $this->response = $response ?: new Response;

        $this->dompdf = $dompdf ?: (class_exists('DOMPDF') ? new DOMPDF : null);

        $this->view = $view ?: new NativeEnvironment;
    }

    /**
     * Default method value.
     *
     * @var int
     */
    protected $defaultMethod = 'single';

    /**
     * Default threshold value.
     *
     * @var int
     */
    protected $defaultThreshold = 100;

    /**
     * Default throttle value.
     *
     * @var int
     */
    protected $defaultThrottle = 100;

    /**
     * Sets the default method.
     *
     * @param  int  $defaultMethod
     * @return void
     */
    public function setDefaultMethod($defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;
    }

    /**
     * Sets the default threshold.
     *
     * @param  int  $defaultThreshold
     * @return void
     */
    public function setDefaultThreshold($defaultThreshold)
    {
        $this->defaultThreshold = $defaultThreshold;
    }

    /**
     * Sets the default throttle.
     *
     * @param  int  $defaultThrottle
     * @return void
     */
    public function setDefaultThrottle($defaultThrottle)
    {
        $this->defaultThrottle = $defaultThrottle;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function getDompdf()
    {
        return $this->dompdf;
    }

    /**
     * {@inheritDoc}
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return $this->request->get('filters', array());
    }

    /**
     * {@inheritDoc}
     */
    public function getSort()
    {
        return $this->request->get('sort');
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection()
    {
        $direction = $this->request->get('direction');

        return in_array($direction, array('asc', 'desc')) ? $direction : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPage()
    {
        $page = (int) $this->request->get('page', 1);

        return ($page > 0) ? $page : 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        $method = $this->request->get('method');

        return $method ?: $this->defaultMethod;
    }

    /**
     * {@inheritDoc}
     */
    public function getThreshold()
    {
        $threshold = (int) $this->request->get('threshold');

        return ($threshold > 0) ? $threshold : $this->defaultThreshold;
    }

    /**
     * {@inheritDoc}
     */
    public function getThrottle()
    {
        $throttle = (int) $this->request->get('throttle');

        return ($throttle > 0) ? $throttle : $this->defaultThrottle;
    }

    /**
     * Returns the download type.
     *
     * @return string
     */
    public function getDownload()
    {
        return $this->request->get('download');
    }

    /**
     * Returns the max allowed results.
     *
     * @return int
     */
    public function getMaxResults()
    {
        return $this->request->get('max_results');
    }

    /**
     * Prepare csv data.
     *
     * @param  array  $results
     * @param  string  $delimiter
     * @return string
     */
    protected function prepareCsv($results, $delimiter = ',')
    {
        if (! $results) {
            return;
        }

        $data      = '';
        $enclosure = '"';

        $header = array_filter(head($results), function ($el) {
            return ! is_array($el);
        });

        $data .= $enclosure.implode($enclosure.$delimiter.$enclosure, array_keys($header)).$enclosure.PHP_EOL;

        foreach ($results as $row) {
            $rowFiltered = array();
            $row = array_filter($row, function ($el) {
                return ! is_array($el);
            });

            foreach ($row as $column) {
                $rowFiltered[] = $enclosure.(str_replace($enclosure, $enclosure . $enclosure, $column)).$enclosure;
            }

            $data .= implode($delimiter, $rowFiltered) . PHP_EOL;
        }

        return $data;
    }

    /**
     * Download results as pdf.
     *
     * @param  array  $results
     * @param  int  $options
     * @param  string  $filename
     * @return void
     */
    public function downloadJson($results, $options = null, $filename = 'data-grid')
    {
        $response = $this->response->create(
            json_encode($results, $options),
            200,
            array(
                'Content-Type'        => 'application/json',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.json"',
            )
        );

        return $response;
    }

    /**
     * Download results as csv.
     *
     * @param  array  $results
     * @param  string  $delimiter
     * @param  string  $filename
     * @param  \Closure  $parser
     * @return void
     */
    public function downloadCsv($results, $delimiter = ',', $filename = 'data-grid', Closure $parser = null)
    {
        if ($parser instanceof Closure) {
            $results = call_user_func_array($parser, array($results, $delimiter));
        } else {
            $results = $this->prepareCsv($results, $delimiter);
        }

        $response = $this->response->create(
            $results,
            200,
            array(
                'Content-Type'        => 'application/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            )
        );

        return $response;
    }

    /**
     * Download results as pdf.
     *
     * @param  array  $results
     * @param  string  $pdfView
     * @param  string  $filename
     * @return void
     * @throws \RuntimeException
     */
    public function downloadPdf($results, $pdfView, $filename = 'data-grid')
    {
        if (! $this->dompdf) {
            throw new RuntimeException('"dompdf/dompdf" is required.');
        }

        $results = $this->flatten($results);

        $keys = array_keys(head($results));

        $html = $this->view->make($pdfView, compact('keys', 'results'));

        $this->dompdf->load_html($html);

        $this->dompdf->render();

        $this->dompdf->stream("{$filename}.pdf");
    }

    /**
     * Flattens results.
     *
     * @param  array  $results
     * @return array
     */
    protected function flatten($results)
    {
        return array_map(function ($result) {
            return array_filter($result, function ($r) {
                return ! is_array($r);
            });
        }, $results);
    }
}
