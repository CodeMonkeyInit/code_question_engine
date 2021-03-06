<?php

use App\Jobs\CheckResultJob;
use CodeQuestionEngine\DockerManager;
use CodeQuestionEngine\EngineGlobalSettings;
use CodeQuestionEngine\CodeTask;
use CodeQuestionEngine\CodeFileManagerFactory;

class TaskStatesManager
{
    /**
     * @var DockerManager
     */
    protected $dockerManager;

    protected $dockerInstance;

    public function __construct(DockerManager $dockerManager)
    {
        $this->dockerManager = $dockerManager;

    }

    public function operateTaskStates()
    {
        $tasks = CodeTask::getAll();

        foreach ($tasks as $task) {

            switch ($task->state) {

                case CodeTaskStatus::Running: {
                    $this->dockerManager->setLanguage($task->language);
                    $this->dockerInstance = $this->dockerManager->getOrCreateInstance();
                    $processInfo = $this->dockerInstance->getProcessInfo($task->processName);
                    $this->changeProcessState($processInfo,$task);
                    $this->pushTasksToChecking($tasks,$task);
                }break;
            }
        }
        return $tasks;
    }


    /**
     * 1 задача == 1 тест кейс. Для отправки на проверку надо, чтобы все тест-кейсы были проверены
     * данный метод возвращает количество проверенных кейсов
     */

    private function pushTasksToChecking(array $allTasks, $currentTask){

        $cases_tasks = $this->getTasksByProgramId($allTasks, $currentTask->programId, $currentTask->dirPath);
        $ready_count = $this->getReadyCaseTasksCount($cases_tasks);

        $isAdminCall = false;
        if ($ready_count == $currentTask->casesCount) {

            foreach ($cases_tasks as $case_task) {


                if($case_task->isAdminTask){
                    $isAdminCall = true;
                }
                $case_task->state = CodeTaskStatus::Checking;
                $case_task->store();

            }

            //если задачу создавал админ, она проверяется синхронно, а не через job, поэтому не создаем
            //новую job
            if(!$isAdminCall) {
                \Queue::push(new CheckResultJob($currentTask->language, $cases_tasks));
            }
        }

    }

    private function getReadyCaseTasksCount($casesTasks){
        $ready = 0;

        foreach ($casesTasks as $case_task) {
            if ($case_task->state != CodeTaskStatus::QueuedToExecute &&
                $case_task->state != CodeTaskStatus::Running
            ) {
                $ready++;
            }
        }
        return $ready;
    }

    private function changeProcessState($processInfo,$task){
        if (!empty($processInfo)) {

            if (($processInfo["memory"] - EngineGlobalSettings::STANDART_MEMORY_USAGE)
                > $task->memoryLimit
            ) {
                $task->state = CodeTaskStatus::MemoryOverflow;
                $this->dockerInstance->killProcess($task->processName);
                echo "убил по лимиту памяти\n";

            }
            if ($processInfo["time"]["seconds"] > $task->timeout) {
                echo "убил по таймауту \n";
                $task->state = CodeTaskStatus::Timeout;
                $this->dockerInstance->killProcess($task->processName);

            }

        } else {
            $task->state = CodeTaskStatus::QueuedToCheck;

        }
        $task->store();

    }

    private function getTasksByProgramId(array $tasks, $programId, $dirPath)
    {
        $result = [];
        foreach ($tasks as $task) {
            if ($task->programId == $programId && $task->dirPath  == $dirPath) {
                $result[] = $task;
            }
        }
        return $result;
    }
}