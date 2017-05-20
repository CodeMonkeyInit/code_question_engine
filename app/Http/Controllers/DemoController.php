<?php


namespace App\Http\Controllers;
use CodeQuestionEngine\CodeTask;
use Repositories\UnitOfWork;
use DockerInfo;



class DemoController
{


    private $_uow;

    public function __construct(UnitOfWork $uow){

        $this->_uow = $uow;
    }
    public function docker(){
    }
}