<?php


class BaseContract
{

    private $code;

    private $programId;

    private $testResultId;

    private $questionId;

    private $userId;

    public function fillFromJson($json){
        $jsonArray = $json;
        foreach($jsonArray as $key=>$value){
            $this->$key = $value;
        }
    }

}