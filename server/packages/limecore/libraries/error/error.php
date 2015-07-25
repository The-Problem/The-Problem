<?php
/**
 * Error handling class
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 2.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package LimeCore.error
 */
class Error {
    /*const LEVEL_NOTICE = 100;
    const LEVEL_WARNING = 101;
    const LEVEL_FATAL = 102;
    const LEVEL_RECOVERABLE = 103;*/
    
    /**
     * Outputs and reports an error
     *
     * The parameters $lvl and $message are swappable.
     *
     * @param int $level Error level to report
     * @param string $message Error message to report
     * @param string $file File which error occured in
     * @param int $line Line of file which error occured in
     */
    public static function report($lvl = NULL, $message = NULL, $file = "", $line = 0, $code = 0) {
        if (is_string($lvl)) {
            $a = $lvl;
            $lvl = $message;
            $message = $a;
        }
        if (is_null($lvl)) $lvl = 0;
        if (is_null($message)) $message = "";
        
        $levels = array(
            E_USER_NOTICE       => 0,
            E_NOTICE            => 0,
            E_STRICT            => 0,
            E_DEPRECATED        => 1,
            E_USER_DEPRECATED   => 1,
            E_WARNING           => 2,
            E_CORE_WARNING      => 2,
            E_COMPILE_WARNING   => 2,
            E_USER_WARNING      => 2,
            E_RECOVERABLE_ERROR => 3,
            E_ERROR             => 4,
            E_CORE_ERROR        => 4,
            E_COMPILE_ERROR     => 4,
            E_USER_ERROR        => 4,
            E_PARSE             => 5
        );
        $lang = array(
            0 => "Notice",
            1 => "Deprecated Notice",
            2 => "Warning",
            3 => "Recoverable Error",
            4 => "Error",
            5 => "Parse Error"
        );
        
        if (!array_key_exists($lvl, $levels)) $lvl = E_USER_ERROR;
        $level = $levels[$lvl];
        $text = $lang[$level];
        
        Logger::add($level, $message, $file, $line, $code);
        
        /*if ($level > 3) {
            $page = Path::getpage();
            if (count($page) > 0 && strtolower($page[0]) === "error") echo "<pre>Oh no, something bad happened!\nPlease try again later.\n\nSpecific error information:\nLevel: " . $lvl . " (" . $text . ")\nMessage: " . $message . "\nFile: " . $file . " (" . $code . ")\nCode: " . $code . "</pre>";
            else Path::redirect(Path::getclientfolder("error", "500"));
        }*/
    }
}