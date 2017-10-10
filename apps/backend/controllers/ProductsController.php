<?php

namespace Apps\Backend\Controllers;

use Phalcon\Mvc\Controller;
use Apps\Backend\Models\Products as Products;

class ProductsController extends Controller
{

    public function indexAction()
    {
        $this->view->product = Products::findFirst();
    }
}
