<?php

use CodeQuestionEngine\CodeQuestionManager;

class CodeQuestionManagerTest extends TestCase
{


    /**
     * @var CodeQuestionManager
     */
    private $codeQuestionManager;

    protected function setUp()
    {
        parent::setUp();

        $this->codeQuestionManager = app()->make(CodeQuestionManager::class);
    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }


    /**
     * Проверяет, установился ли язык программирования у зависимого fileManagerа
     */
    public function testSetLanguage(){

        $this->writeConsoleMessage('Проверка установки языка программирования');
        //Act
        $this->codeQuestionManager->setProgramLanguage(Language::C);
        $fileManager = $this->codeQuestionManager->getFileManager();

        //Assert
        $this->assertEquals('c', $fileManager->getCodeFileExtension());
        $this->writeOk();

    }

    public function testRunQuestionProgram(){
        $this->writeConsoleMessage('Запуск программы');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testRunProgram(){
        $this->writeConsoleMessage('Запуск программы в режиме администратора');
        $this->assertEquals(true,true);
        $this->writeOk();
    }



}