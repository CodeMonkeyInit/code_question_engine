<?php


/**
 * Class Language - языки программирования, поддерживаемые системой
 */
class Language
{

    const C = 1;
    const PHP = 2;
    const Pascal = 3;
    const C_PLUS = 4;
    const C_C_PLUS = 5;
    const JavaScript = 6;


    public static function getLanguageAlias($lang){
        switch($lang){
            case Language::C: return "C";
            case Language::PHP: return "PHP";
            case Language::Pascal: return "Pascal";
            case Language::JavaScript: return "JavaScript";
            default: return "default";
        }
    }

    public static function getLanguageByAlias($alias){
        switch($alias){
            case "C": return self::C;
            case "PHP": return self::PHP;
            case "Pascal": return self::Pascal;
            case "JavaScript": return self::JavaScript;
            default: return 0;
        }
    }

}