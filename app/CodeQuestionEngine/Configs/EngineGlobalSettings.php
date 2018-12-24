<?php

namespace CodeQuestionEngine;


class EngineGlobalSettings
{
    /**
     * Название папки, хранящей сборочные файлы, шелл-скрипты
     * Общая папка между виртуальным и реальным хостом
     */
    const CACHE_DIR = "temp_cache";


    /**
     * Название docker-образа операционной системы
     */
    const IMAGE_NAME = "baseimage-ssh";


    /**
     * имя шаблона шелл-скрипта, запускающего код на выполнение.
     * Инструкция по созданию новых шаблонов шелл-скриптов находится в документации
     * Для добавления поддержки нового языка добавьте название скрипта сюда
     * Пример шаблона шелл скрипта для языка С:
     *
     * gcc code.c 2> errors.txt -o
     * run
     * Вместо строки run программа подставит необходимые данные для запуска кода
     *
     * Имя скрипта ОБЯЗАТЕЛЬНО должно заканчиваться на .sh
     */
    const SHELL_SCRIPT_NAME_ARRAY = [
        \Language::C => "run.sh",
        \Language::PHP => "php-run.sh",
        \Language::Pascal => "pascal-run.sh",
        \Language::JavaScript => "node-run.sh"
    ];


    /**
     * Массив с расширениями файлов для конкретного языка
     */
    const CODE_FILE_EXTENSIONS_ARRAY = [
        \Language::C => 'c',
        \Language::PHP => "php",
        \Language::Pascal => "pas",
        \Language::JavaScript => "js"
    ];

    /**
     * имя исполняемого файла для конкретного языка
     * Для скриптовых языков оставьте пустую строку
     */
    const EXECUTE_FILE_NAME = [
        \Language::C => "c_output.out",
        \Language::PHP => "",
        \Language::Pascal => "code",
        \Language::JavaScript => ""
    ];

    /**
     * Служебное слово для запуска
     */
    const RUN_WORD = [
        \Language::C => './',
        \Language::PHP => 'php ',
        \Language::Pascal => './',
        \Language::JavaScript => 'node '
    ];

    /**
     * Имя входного файла для отладки в режиме администратора
     */
    const INPUT_FILE_NAME = "input.txt";

    /**
     * Имя выходного файла для отладки в режиме администратора
     */
    const OUTPUT_FILE_NAME = "output.txt";

    /**
     * Ключевое слово, используемое в шаблонном шелл-скрипте, вместо которого подставляются команды для запуска
     *
     */
    const KEY_WORD_TO_PUT_RUN_INFO = "run";

    /**
     * Файл с ошибками времени компиляции
     *
     */
    const ERRORS_FILE = "errors.txt";

    /**
     * Имя входного файла для тестового набора параметров. Вместо звездочки подставляется номер кейса
     */
    const INPUT_FILE_NAME_FOR_TEST_CASE = "test_input_*.txt";

    /**
     * Имя выходного файла с ожидаемым результатом для конкретного тестового случая
     */
    const OUTPUT_FILE_FOR_EXPECTED_RESULT = "test_output_*.txt";

    /**
     * Имя выходного файла для тестового набора параметров. Вместо звездочки подставляется номер кейса
     */
    const OUTPUT_FILE_NAME_FOR_TEST_CASE = "student_output_*.txt";

    /**
     * Имя исходника с кодом. Вместо звездочки подставляется расширение для конкретного языка
     */
    const CODE_FILE_NAME = "code.*";

    /**
     * Паттерн строки запуска кода
     * Вместо $0 - имя служебной команды для запуска
     * Вместо $1 - имя скрипта или exe-шника
     * Вместо $2 - имя выходного файла, куда перенаправляется выходной поток программы
     * Вместо $3 - имя входного файла, куда перенаправляется входной поток программы
     */
    const EXECUTE_PATTERN = "$0$1 1> $2 < $3";
    const EXECUTE_PATTERN_FOR_SCRIPT_LANGUAGES = "$0$1 1> $2 < $3 2> errors.txt";

    /**
     * Имя папки, где лежает шаблонные шелл-скрипты
     */
    const BASE_SHELL_SCRIPT_DIR_NAME = "ExecutionScripts";

    /**
     * Ключевое слово в шаблонном шелл-скрипте, вместо которого подставляется имя exe файла или выполняемого скрипта
     * для скриптовых языков.
     * Для С это a.out
     * Для пхп это name.php
     */
    const OBJECT_FILE_KEY_WORD = "exe";

    /**
     * Стандартный размер потребляемой памяти пустого процесса
     */
    const STANDART_MEMORY_USAGE = 4224;


}