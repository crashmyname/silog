<?php

namespace App\Controllers;

use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class AdminController extends BaseController
{
    // Controller logic here
    public function index()
    {
        return $this->view('admin/index');
    }
}
