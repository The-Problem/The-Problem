<?php
/**
 * Database-based logging system
 *
 * Used to log information and errors to the database for easy viewing.
 * If the log list isn't saved, it will be updated the next time the logger starts
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */
class Logger {
    private static $table = "logs";
    
    /**
     * Starts the logger, and also updates the database if any logs weren't saved.
     *
     * @param string $table The table to store logs in, default is "logs"
     */
    public static function start($table = "logs") {
        self::$table = $table;
    }
    
    /**
     * Adds an entry to the logger
     *
     * The first two parameters ($level and $message) can be swapped around
     *
     * @param int $level The error level, from 0 - 5, default is 0
     * @param string $message The message for the entry, default is ""
     * @param string $file The file the entry occured in, default is ""
     * @param int $line The line of the file, default is 0
     * @param int $code The message (error) code, default is 0
     */
    public static function add($level = NULL, $message = NULL, $file = "", $line = 0, $code = 0) {
        //if (!Cookies::exists("logger_backup")) Cookies::prop("logger_backup", array());
        
        if (is_string($level)) {
            $c = $level;
            $level = $message;
            $message = $c;
        }
        
        if (is_null($level)) $level = 0;
        if (is_null($message)) $message = "";
                
        Connection::query("INSERT INTO `" . self::$table . "` (level, message, file, line, code, time) VALUES (?, ?, ?, ?, ?, ?)", "issiis", array(
            $level, $message, $file, $line, $code, date('Y-m-d H:i:s')
        ));
    }
}