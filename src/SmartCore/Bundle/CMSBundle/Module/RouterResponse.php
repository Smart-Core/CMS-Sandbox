<?php

namespace SmartCore\Bundle\CMSBundle\Module;

use SmartCore\Bundle\CMSBundle\Response;

class RouterResponse extends Response
{
    protected $controller = null;
    protected $action = null;
    protected $arguments = [];
    protected $breadcrumbs = [];

    /**
     * Constructor.
     *
     * @param string  $content The response content
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     *
     * @api
     */
    public function __construct($content = '', $status = 200, $headers = [])
    {
        parent::__construct($content, 404, $headers);
    }

    /**
     * @return string|null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $name
     */
    public function setController($name)
    {
        $this->setStatusCode(200);
        $this->controller = $name;
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $name
     */
    public function setAction($name)
    {
        $this->setStatusCode(200);
        $this->action = $name;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * @return array
     */
    public function getAllArguments()
    {
        return $this->arguments;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setArgument($name, $value)
    {
        $this->setStatusCode(200);
        $this->arguments[$name] = $value;
    }

    /**
     * @param string $uri
     * @param string $title
     * @param bool $descr
     */
    public function addBreadcrumb($uri, $title, $descr = false)
    {
        $this->breadcrumbs[] = [
            'uri'   => $uri,
            'title' => $title,
            'descr' => $descr,
        ];
    }

    /**
     * @return array
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}