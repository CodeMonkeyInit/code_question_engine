<?php


namespace Repositories;


use Doctrine\ORM\EntityManager;


class UnitOfWork
{
    private $_em;

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }


    private $_questionRepo;
    private $_givenAnswerRepo;
    private $_programRepo;
    private $_dockerInfoRepo;
    private $_userRepo;
    private $_testResultRepo;



    public function questions(){
        if ($this->_questionRepo == null){
            $this->_questionRepo = new QuestionRepository($this->_em);
        }
        return $this->_questionRepo;
    }



    public function givenAnswers(){
        if ($this->_givenAnswerRepo == null){
            $this->_givenAnswerRepo = new GivenAnswerRepository($this->_em);
        }
        return $this->_givenAnswerRepo;
    }


    public function programs(){
        if ($this->_programRepo == null){
            $this->_programRepo = new ProgramRepository($this->_em);
        }
        return $this->_programRepo;
    }

    public function dockerInfos(){
        if($this->_dockerInfoRepo == null){
            $this->_dockerInfoRepo = new DockerInfoRepository($this->_em);
        }
        return $this->_dockerInfoRepo;
    }

    public function users(){
        if ($this->_userRepo == null){
            $this->_userRepo = new UserRepository($this->_em);
        }
        return $this->_userRepo;
    }

    public function testResults(){
        if ($this->_testResultRepo == null){
            $this->_testResultRepo = new TestResultRepository($this->_em);
        }
        return $this->_testResultRepo;
    }

    /**
     * Применяет к базе данных изменения, сделанные втечение сессии.
     */
    public function commit(){
        $this->_em->flush();
    }

    /**
     * Отсоединяет сущность от контеста БД.
     * @param $entity
     */
    public function detach($entity){
        $this->_em->detach($entity);
    }

    /**
     * Обновляет сущность, полученную из БД.
     * (из-за кэширования ORM некоторых получаемых из БД данных, могут возвращаться неактуальные значения).
     * @param $entity
     */
    public function refresh($entity){
        $this->_em->refresh($entity);
    }
}