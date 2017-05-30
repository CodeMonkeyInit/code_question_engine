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
     * @var DockerManager
     */
    private $dockerManager;

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


    public function __construct(DockerManager $dockerManager)
    {
        $this->dockerManager = $dockerManager;
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
            $paramSets = $contract->getParamSets();
            $timeLimit = $contract->getTimeLimit();
            $userFio = $contract->getFio();

            $givenAnswerId = $this->createEmptyAnswerEntity($testResultId, $questionId, $code);

            $this->prepareForRunning($code, $userFio);
            $cases_count = $this->fileManager->createTestCasesFiles($paramSets);

            $this->run($cases_count, $programId, $timeLimit, $memoryLimit, $givenAnswerId);
        }
        catch(Exception $e){

            $errorMsg = $e->getMessage();
            throw new Exception("Запуск программы произошел с ошибкой. $errorMsg");
        }

        return "Программа успешно запущена";
    }


    /**
     * Синхронно запускает программу на выполнение.
     * Возвращает полную информацию(результаты + ошибки)
     * На вход передается программный код и массив объектов ParamSet
     * @param $contract
     * @return string
     */
    public function runProgram(RunProgramDataContract $contract){

        $this->prepareForRunning($contract->getCode(), $contract->getFio());
        $cases_count = $this->fileManager->createTestCasesFiles($contract->getParamSets());

        $dirName = $this->fileManager->getDirNameFromFullPath();
        $cache_dir = $this->fileManager->getCacheDirName();

        $this->dockerManager->setLanguage($contract->getLanguage());
        $dockerInstance = $this->dockerManager->getOrCreateInstance();


        $commands_to_run = array();
        $executeFileNames = array();
        $program_id = random_int(1,1000);

        //Здесь мы не сразу запускаем на выполнение, т.к.
        //если запускать сразу, то возникает неуловимый баг


        for($i = 0; $i < $cases_count; $i++) {
            //здесь в качестве programId передаем что угодно, т.к. этот метод вызывает админ
            //и по факту программы в бд нет.
            $result = $this->fileManager->createShellScriptForTestCase(1, $i);
            $script_name = $result["scriptName"];

            $executeFileNames[] = $result["executeFileName"];
            $commands_to_run[] = "sh /opt/$cache_dir/$dirName/$script_name";
        }

        $codeTasks = array();
        for($i = 0; $i< count($commands_to_run); $i++){
            $codeTask = new CodeTask($program_id
                ,1
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileNames[$i]
                ,\CodeTaskStatus::Running
                ,$contract->getTimeLimit()
                ,$contract->getMemoryLimit()
                ,$cases_count
                ,$i
                ,true);
            $codeTask->store();

            $codeTasks[] = $codeTask;
            $dockerInstance->run($commands_to_run[$i]);
        }


        $errors = $this->fileManager->getErrors();
        $mark = $this->fileManager->calculateMarkForAdmin($codeTasks);
        $results = $this->fileManager->getResultsForCompare($cases_count);


        if(empty($errors)){
            $errors = "Отсутствуют";
        }
        $result_message = "Ошибки компиляции: $errors\n\n"."Оценка: $mark/100"."\n\n$results\n"."";

        return $result_message;

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