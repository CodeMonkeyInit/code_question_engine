<?php


namespace App\Http\Controllers;
use CodeQuestionEngine\CodeQuestionManager;
use Illuminate\Http\Request;
use RunProgramDataContract;
class CodeQuestionController
{


    /**
     * @var CodeQuestionManager
     */
    private $_codeQuestionManager;

    public function __construct(CodeQuestionManager $manager)
    {
        $this->_codeQuestionManager = $manager;
    }

    /**
     * @param Request $request запускает программу на выполнение
     * @return
     */
    public function runProgram(Request $request){

        $runProgramJson = $request->all();
        $runProgramDataContract = new RunProgramDataContract();
        $runProgramDataContract->fillFromJson($runProgramJson);

        $this->_codeQuestionManager->setProgramLanguage($runProgramDataContract->getLanguage());
        $this->_codeQuestionManager->runQuestionProgram($runProgramDataContract);

    }
}