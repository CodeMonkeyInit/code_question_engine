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


        $info = new DockerInfo();


        $info->setContainerId("test");
        $info->setLang(1);
        $info->setId(1);
        $this->_uow->dockerInfos()->create($info);

        $this->_uow->commit();

        dd();

        /* $task = new CodeTask(0
            ,1
            , 2
            , 3
            , 4
            , 5
            , 6
            , 7);
        $task->programId =10;
        $task->store();*/
        CodeTask::flush();
        $tasks = CodeTask::getAll();
        dd($tasks);
    }
}