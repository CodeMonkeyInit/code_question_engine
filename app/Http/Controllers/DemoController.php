<?php


namespace App\Http\Controllers;
use CodeQuestionEngine\CodeTask;


class DemoController
{

    public function docker(){


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