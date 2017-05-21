<?php
namespace CodeQuestionEngine;

use App\Jobs\RunProgramJob;
use Queue;
use RunProgramDataContract;
use Exception;
use TestCalculatorProxy;

class CodeQuestionManager
{


    /**
     * @var \CodeFileManager
     */
    private $fileManager;

    /**
     * @var \Language язык программирования
     */
    private $language;

    /**
     * @return \CodeFileManager
     */
    public function getFileManager()
    {
        return $this->fileManager;
    }

    /**
     * @return \Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $lang - Устанавливает язык программирования, за который отвечает данный менеджер
     * Инстанциирует необходимые зависимости для работы с конкретным языком программирования
     */
    public function setProgramLanguage($lang){
        $this->language = $lang;
        $this->fileManager = CodeFileManagerFactory::getCodeFileManager($lang);
    }

    /**
     * Запускает код на выполнение с входными параметрами, которые берутся из базы и заполняются преподавателем при
     * добавлении вопроса. Возвращает оценку студента
     * @param RunProgramDataContract $contract
     * @return string оценка
     * @throws Exception
     */
    public function runQuestionProgram(RunProgramDataContract $contract)
    {
        try {

            $code = $contract->getCode();

            $testResultId = $contract->getTestResultId();
            $questionId = $contract->getQuestionId();
            $programId = $contract->getProgramId();
            $memoryLimit = $contract->getMemoryLimit();
            $timeLimit = $contract->getTimeLimit();
            $userFio = $contract->getFio();

            $givenAnswer = $this->createEmptyAnswerEntity($testResultId, $questionId, $code);
            $this->prepareForRunning($code, $userFio);
            $cases_count = $this->fileManager->createTestCasesFiles($contract->getProgramId());

            $this->run($cases_count, $programId, $timeLimit, $memoryLimit, $givenAnswer->getId());
        }
        catch(Exception $e){

            $errorMsg = $e->getMessage();
            throw new Exception("Запуск программы произошел с ошибкой. $errorMsg");
        }

        return "Программа успешно запущена";
    }

    /**
     * Запускает код на выполнение с входными параметрами, которые передаются в виде массива. Возвращает результат работы программы
     * @param $code
     * @param array $paramSets
     * @return mixed
     * @throws \Exception
     */
    public function runQuestionProgramWithParamSets($code,array $paramSets){


            $this->prepareForRunning($code);

            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets);


            //метод для админа, поэтому programId 0. Это значение несущественно
            $this->run($cases_count,0);

            $result = 'finished';

            /*$errors = $this->fileManager->getErrors();
            if($errors != ''){
                throw new \Exception($errors);
            }


           $result =  $this->fileManager->calculateMark($cases_count);
            $result.="\n";
            $result.= $this->fileManager->getResultsForCompare($cases_count);

            $this->fileManager->putLogInfo($result);
           */
            return $result;
    }


    /**
     * @param $programId
     * @param $timeLimit
     * @param $memoryLimit
     * @param $givenAnswerId
     * @param $cases_count
     */
    private function run($cases_count, $programId,$timeLimit,$memoryLimit,$givenAnswerId){
        $dirName = $this->fileManager->getDirNameFromFullPath();
        $cache_dir = $this->fileManager->getCacheDirName();

        if($cases_count == 0){
            $this->fileManager->createInputFile();
            $result = $this->fileManager->createShellScript();
            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];

            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($programId
                ,$givenAnswerId
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$timeLimit
                ,$memoryLimit
                ,1);
            $codeTask->store();

            Queue::push(new RunProgramJob($command,$codeTask));
            return;
        }

        for($i = 0; $i < $cases_count; $i++) {
            $result = $this->fileManager->createShellScriptForTestCase($programId, $i);


            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];


            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($programId
                ,$givenAnswerId
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$timeLimit
                ,$memoryLimit
                ,$cases_count
                ,$i);

            $codeTask->store();


            Queue::push(new RunProgramJob($command,$codeTask));
        }


    }

    private function createEmptyAnswerEntity($testResultId,$questionId,$code){
        $givenAnswerId = TestCalculatorProxy::createEmptyAnswerEntity($testResultId,$questionId,$code);
        return $givenAnswerId;
    }
    private function prepareForRunning($code,$userFio){
        $dirPath = $this->fileManager->createDir($userFio);
        $this->fileManager->setDirPath($dirPath);
        $this->fileManager->putCodeInFile($code);
        $this->fileManager->createLogFile();


    }




}