<?php

namespace MarcusGaius\WordPressHeadless\Bootstrap;

class Utils
{
    /**
     * Escapes non valid url characters
     *
     * @param String $text
     * @return String
     */
    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Outputs the parameter to the log file at %THEME%/log.log
     *
     * @param mixed $log
     * @return void
     */
    public function write_log($log): void
    {
        error_log(print_r($log, true) . "\r\n", 3, get_template_directory() . '/log.log');
    }

    public function dd($var = '')
    {
        var_dump($var);
        die;
    }
}
