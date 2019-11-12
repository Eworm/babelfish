<?php

namespace Statamic\Addons\Babelfish\Controllers;

use Statamic\Extend\Controller;

class BabelfishController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view('index');
    }
}
