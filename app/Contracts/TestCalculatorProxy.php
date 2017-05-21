<?php

use Ixudra\Curl\Facades\Curl;
class TestCalculatorProxy
{

 public static function setAnswerMark($givenAnswerId, $mark){


     $baseUrl = ConnectionConfigSettings::$BASE_URL;
     $action = ConnectionConfigSettings::$CALCULATE_MARK_URL;

     $data = json_encode(['answerId' => $givenAnswerId, 'mark' => $mark]);

     $response = Curl::to($baseUrl.'/'.$action)
         ->withData($data)
         ->post();

     return $response;
 }


 public static function createEmptyAnswerEntity($testResultId,$questionId,$code){

        $baseUrl = ConnectionConfigSettings::$BASE_URL;
        $action = ConnectionConfigSettings::$CREATE_GIVEN_ANSWER;

        $contract  = new GivenAnswerData();
        $contract->setTestResultId($testResultId);
        $contract->setQuestionId($questionId);
        $contract->setCode($code);
        $response = Curl::to($baseUrl.'/'.$action)
            ->withData($contract->jsonSerialize())
            ->post();

        return $response;
 }

}