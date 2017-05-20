<?php


namespace App\Http\Controllers;

use Repositories\UnitOfWork;




class DemoController
{


    private $_uow;

    public function __construct(UnitOfWork $uow){

        $this->_uow = $uow;
    }
    public function docker(){
    }
}