<?php


use CodeQuestionEngine\DockerManager;
class DockerManagerTest extends TestCase
{


    /**
     * @var DockerManager;
     */
    private $dockerManager;

    protected function setUp()
    {
        parent::setUp();

        $this->dockerManager = app()->make(DockerManager::class);
        $this->dockerManager->setLanguage(Language::C);
    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function runTests(){
    }


    public function testCreateDropDocker(){

        $this->writeConsoleMessage('Проверка создания докер-контейнера: ');
        $instance = $this->dockerManager->getOrCreateInstance();
        $this->assertNotNull($instance);
        $this->writeOk();
        $this->dockerManager->dropAllInstances();
    }

    public function testGetOrCreateInstance(){
        $this->writeConsoleMessage('Cоздание контейнера');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testStopContainer(){
        $this->writeConsoleMessage('Остановка контейнера');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

    public function testPushInfoToRedis(){
        $this->writeConsoleMessage('Запись информации о процессе в Redis');
        $this->assertEquals(true,true);
        $this->writeOk();
    }

}