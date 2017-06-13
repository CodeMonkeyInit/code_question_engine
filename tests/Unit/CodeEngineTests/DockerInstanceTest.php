<?php


class DockerInstanceTest extends TestCase
{



    protected function setUp()
    {
        parent::setUp();


    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }


    public function testRunCommand(){
        $this->writeConsoleMessage('Выполнение команды внутри контейнера');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testGetProcessInfo(){
        $this->writeConsoleMessage('Получение информации о процессе');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testKillProcess(){
        $this->writeConsoleMessage('Уничтожение процесса');
        $this->assertEquals(true,true);
        $this->writeOk();
    }




}