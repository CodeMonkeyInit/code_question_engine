<?php

class ConnectionConfigSettings
{


    /**
     * @var string URL основного сервиса
     */
    public static $BASE_URL = "www.web-test.ru";

    public static $CALCULATE_MARK_URL = "/api/external/setMark";


    /**
     * @var array Белый список IP  для пользования внешними модулями
     */
    public static $WHITE_LIST = ["127.0.0.1","192.168.0.100"];

}