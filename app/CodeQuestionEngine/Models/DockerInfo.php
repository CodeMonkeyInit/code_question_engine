<?php

/**
 * Модель, инкапсулирующая информацию о запущенном докер-контейнере
 * хранящейся в Redis Cache.
 * @package СodeQuestionEngine
 */


namespace CodeQuestionEngine;

use Illuminate\Support\Facades\Redis;

class DockerInfo
{


    public $key;

    public $containerId;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * @param mixed $containerId
     */
    public function setContainerId($containerId)
    {
        $this->containerId = $containerId;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public $language;


    /**
     * Префикс ключа задачи с кодом для хранения в кеше
     */
    const  prefix = "docker";


    /**
     * CodeTask constructor.

     */
    public function __construct($dockerId,$language,$key = "")
    {
        $this->containerId = $dockerId;
        $this->language = $language;
        if($key == "") {
            $this->key = self::prefix . '-' . $language;
        }
        else{
            $this->key = $key;
        }
    }

    public function store(){

        Redis::hmset($this->key, [
            'key'      => $this->key,
            'containerId' => $this->containerId,
            'language' => $this->language,
        ]);
    }


    public static function find($key)
    {
        $stored = Redis::hgetall($key);
        if (!empty($stored)) {
            return new DockerInfo(
                  $stored['containerId']
                , $stored['language']
                , $stored['key']);
        }
        return false;
    }

    public static function getAll()
    {
        $prefix = self::prefix;
        $keys = Redis::keys("$prefix-*");
        $tasks = [];
        foreach ($keys as $key) {
            $stored = Redis::hgetall($key);
            $task = new DockerInfo(
                  $stored['containerId']
                , $stored['language']
                , $stored['key']);

            $tasks[] = $task;
        }
        return $tasks;
    }

    public static function findByLang($lang){

        $prefix = self::prefix;
        $keys = Redis::keys("$prefix-$lang");
        $tasks = [];
        foreach ($keys as $key) {
            $stored = Redis::hgetall($key);
            $task = new DockerInfo(
                  $stored['containerId']
                , $stored['language']
                , $stored['key']);

            $tasks[] = $task;
        }
        return $tasks;
    }

    public static function flush(){
        $prefix = self::prefix;
        $keys = Redis::keys("$prefix-*");
        foreach ($keys as $key) {
            Redis::del($key);
        }
    }

    public function delete(){
        Redis::del($this->key);
    }

    public static function deleteByKey($key){
        Redis::del($key);
    }

}