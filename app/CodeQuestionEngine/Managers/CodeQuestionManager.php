<?php
namespace CodeQuestionEngine;

use Auth;
use GivenAnswer;
use Repositories\UnitOfWork;
use App\Jobs\RunProgramJob;
use Queue;
use RunProgramDataContract;

class CodeQuestionManager
{


    /**
     * @var \CodeFileManager
     */
    private $fileManager;
    /**
     * @var UnitOfWork
     */
    private $_uow;



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


    public function __construct(UnitOfWork $uow)
    {
        $this->_uow = $uow;
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

     * @return string оценка
     */
    public function runQuestionProgram(RunProgramDataContract $contract)
    {
        try {

            $code = $contract->getCode();
            $program = $this->_uow->programs()->find($contract->getProgramId());
            $testResult = $this->_uow->testResults()->find($contract->getTestResultId());
            $question = $this->_uow->questions()->find($contract->getQuestionId());
            $user = $this->_uow->users()->find($contract->getUserId());

            $givenAnswer = $this->createEmptyAnswerEntity($testResult, $question, $code);
            $this->prepareForRunning($code, $user);
            $cases_count = $this->fileManager->createTestCasesFiles($program->getId());

            $this->run($cases_count, $program, $givenAnswer->getId());
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
     * @param object $program
     * @param $cases_count
     */
    private function run($cases_count, $program,$givenAnswerId){
        $dirName = $this->fileManager->getDirNameFromFullPath();
        $cache_dir = $this->fileManager->getCacheDirName();

        if($cases_count == 0){
            $this->fileManager->createInputFile();
            $result = $this->fileManager->createShellScript();
            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];



            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($program->getId()
                ,$givenAnswerId
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$program->getTimeLimit(),$program->getMemoryLimit(),1);
            $codeTask->store();

            Queue::push(new RunProgramJob($command,$codeTask));
            return;
        }

        for($i = 0; $i < $cases_count; $i++) {
            $result = $this->fileManager->createShellScriptForTestCase($program->getId(), $i);


            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];


            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($program->getId()
                ,$givenAnswerId
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$program->getTimeLimit(),$program->getMemoryLimit(),$cases_count,$i);

            $codeTask->store();


            Queue::push(new RunProgramJob($command,$codeTask));
        }


    }

    private function createEmptyAnswerEntity($testResult,$question,$code){
        //пустая сущность ответа на вопрос, потому что это костыль
        $givenAnswer = new GivenAnswer();
        $givenAnswer->setAnswer($code);
        $givenAnswer->setQuestion($question);
        $givenAnswer->setTestResult($testResult);
        $this->_uow->givenAnswers()->create($givenAnswer);
        $this->_uow->commit();
        return $givenAnswer;

    }
    private function prepareForRunning($code,$user){
        $dirPath = $this->fileManager->createDir($user);
        $this->fileManager->setDirPath($dirPath);
        $this->fileManager->putCodeInFile($code);
        $this->fileManager->createLogFile();


    }




}