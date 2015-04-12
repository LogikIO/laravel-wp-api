<?php namespace Cyberduck\LaravelWpApi;

use GuzzleHttp\Client;

class WpApi
{

    protected $client;

    public function __construct($endpoint, Client $client)
    {
        $this->endpoint = $endpoint;
        $this->client   = $client;
    }

    public function posts($page = null)
    {
        return $this->_get('posts', ['page' => $page]);
    }

    public function pages($page = null)
    {
        return $this->_get('posts', ['type' => 'page', 'page' => $page]);
    }

    public function post($slug)
    {
        return $this->_get('posts', ['filter' => ['name' => $slug]]);
    }
    
    public function page($slug)
    {
        return $this->_get('posts', ['type' => 'page', 'filter' => ['name' => $slug]]);
    }

    public function author($name)
    {
        return $this->_get('posts', ['type' => 'page', 'filter' => ['author_name' => $name]]);
    }

    public function categories()
    {
        return $this->_get('taxonomies/category/terms');
    }

    public function tags()
    {
        return $this->_get('taxonomies/post_tag/terms');
    }

    public function category_posts($slug, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['category_name' => $slug]]);
    }

    public function search($query, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['s' => $query]]);
    }

    public function archive($year, $month, $page = null)
    {
        return $this->_get('posts', ['page' => $page, 'filter' => ['year' => $year, 'monthnum' => $month]]);
    }

    public function _get($method, array $query = array())
    {

        try {

            $response = $this->client->get($this->endpoint . '/wp-json/' . $method, ['query' => $query]);

            $return = [
                'results' => $response->json(),
                'total'   => $response->getHeader('X-WP-Total'),
                'pages'   => $response->getHeader('X-WP-TotalPages')
            ];

        } catch (\GuzzleHttp\Exception\TransferException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

            $return = [
                'error'   => $error,
                'results' => [],
                'total'   => 0,
                'pages'   => 0
            ];

        }

        return $return;

    }

}
