<?php


namespace App\Http\Controllers;

use CodeQuestionEngine\DockerInfo;
use CodeQuestionEngine\DockerManager;
use Repositories\UnitOfWork;
use TestCalculatorProxy;



class DemoController
{



    public function __construct(){

    }
    public function docker(){




        $result = TestCalculatorProxy::createEmptyAnswerEntity(1,1,"1488");

        return $result;
        //return TestCalculatorProxy::setAnswerMark(1,100);
    }
}