<?php


namespace App\Http\Controllers;

use CodeQuestionEngine\CodeFileManagerFactory;
use CodeQuestionEngine\DockerInfo;
use CodeQuestionEngine\DockerManager;

use TestCalculatorProxy;



class DemoController
{


    /**
     * @var \CodeFileManager
     */
    private $fileManager;

    public function __construct(){

    }
    public function docker(){

        for($i = 0; $i<10; $i++) {
            $this->fileManager = CodeFileManagerFactory::getCodeFileManager(\Language::C);

            $result = $this->fileManager->createDir("Сухоруких_Кирилл_Всеволодович" . random_int(10, 100000));

        }
        return $result;


        $result = TestCalculatorProxy::createEmptyAnswerEntity(1,1,"1488");

        return $result;
        //return TestCalculatorProxy::setAnswerMark(1,100);
    }
}