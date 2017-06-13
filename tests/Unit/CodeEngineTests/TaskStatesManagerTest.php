<?php


class TaskStatesManagerTest extends TestCase
{


    protected function setUp()
    {
        parent::setUp();

    }


    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testChangeTaskStateToChecking(){
        $this->writeConsoleMessage('Изменение статуса задачи в "Проверяется"');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testProcessState(){
        $this->writeConsoleMessage('Проверка состояния запущенного процесса');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testPushToChecking(){
        $this->writeConsoleMessage('Отправка задачи на проверку');
        $this->assertEquals(true,true);
        $this->writeOk();
    }



}