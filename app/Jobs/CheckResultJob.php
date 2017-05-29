<?php

namespace App\Jobs;


use CodeQuestionEngine\CodeFileManagerFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TestCalculatorProxy;
use Exception;

class CheckResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var \Language
     */
    protected $language;

    /**
     * @var  \CodeFileManager
     */
    protected $fileManager;

    /**
     * @var array
     */
    protected $codeTasks;

    /**
     * Create a new job instance.
     *
     */

    public function __construct($lang, array $codeTasks)
    {
        $this->codeTasks = $codeTasks;
        $this->language = $lang;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->fileManager = CodeFileManagerFactory::getCodeFileManager($this->language);
            $this->fileManager->setDirPath($this->codeTasks[0]->dirPath);


            $count = count($this->codeTasks);
            echo "cases_count = $count\n";
            $mark = $this->fileManager->calculateMark($this->codeTasks);
            echo "оценка $mark\n";

            TestCalculatorProxy::setAnswerMark($this->codeTasks[0]->givenAnswerId, $mark);

            foreach ($this->codeTasks as $codeTask) {
                $codeTask->delete();
                echo $codeTask->key . " задача удалена из кеша\n";
            }

            return;
        }
        catch(Exception $e){
            echo $e->getMessage();
            foreach ($this->codeTasks as $codeTask) {
                $codeTask->delete();
                echo $codeTask->key . " задача удалена из кеша\n";
            }
            return;
        }
    }
}
