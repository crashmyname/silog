<?php

namespace App\Controllers;

use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class HomeController extends BaseController
{
    // Controller logic here
    public function index()
    {
        return $this->view('home/index',['title'=>'Home Page']);
    }
}
