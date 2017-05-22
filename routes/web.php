<?php



Route::get('/', function () {
    return view('welcome');
});


Route::get('docker','DemoController@docker');



Route::group(['prefix' => 'api'], function() {

    /*------------------------------------------------------------------------------
     *                       Программы
     * -----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'program'], function () {

        Route::post('runQuestionProgram','CodeQuestionController@runQuestionProgram')
            ->middleware('checkIP');

        Route::post('runProgram','CodeQuestionController@runProgram')
            ->middleware('checkIP');

    });
});