<?php
/**
 * Provides information about a HTTP response
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package Core.Path
 */
class Response {
    private $header;
    private $body;
    private $info;
    
    /**
     * Constructor for Response
     *
     * @param string $response Response text from cURL
     * @param array $info Info from cURL
     */
    public function __construct($response, $info) {
        $this->header = trim(substr($response, 0, $info['header_size']));
        $this->body = substr($response, $info['header_size']);
        $this->info = $info;
    }
    
    /**
     * Gets the value of the 'url' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function url() {
        return $this->info['url'];
    }
    
    /**
     * Gets the value of the 'content_type' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function contenttype() {
        return $this->info['content_type'];
    }
    
    /**
     * Gets the value of the 'http_code' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function code() {
        return $this->info['http_code'];
    }
    
    /**
     * Gets the value of the 'header_size' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function headersize() {
        return $this->info['header_size'];
    }
    
    /**
     * Gets the value of the 'request_size' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function requestsize() {
        return $this->info['request_size'];
    }
    
    /**
     * Gets the value of the 'filetime' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function filetime() {
        return $this->info['filetime'];
    }
    
    /**
     * Gets the value of the 'ssl_verify_result' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function sslresult() {
        return $this->info['ssl_verify_result'];
    }
    
    /**
     * Gets the value of the 'redirect_count' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function redirectcount() {
        return $this->info['redirect_count'];
    }
    
    /**
     * Gets the value of the 'total_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function time() {
        return $this->info['total_time'];
    }
    
    /**
     * Gets the value of the 'namelookup_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function lookuptime() {
        return $this->info['namelookup_time'];
    }
    
    /**
     * Gets the value of the 'connect_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function connecttime() {
        return $this->info['connect_time'];
    }
    
    /**
     * Gets the value of the 'pretransfer_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function pretransfertime() {
        return $this->info['pretransfer_time'];
    }
    
    /**
     * Gets the value of the 'size_upload' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function uploadsize() {
        return $this->info['size_upload'];
    }
    
    /**
     * Gets the value of the 'size_download' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function downloadsize() {
        return $this->info['size_download'];
    }
    
    /**
     * Gets the value of the 'speed_upload' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function uploadspeed() {
        return $this->info['speed_upload'];
    }
    
    /**
     * Gets the value of the 'speed_download' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function downloadspeed() {
        return $this->info['speed_download'];
    }
    
    /**
     * Gets the value of the 'download_content_length' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function downloadlength() {
        return $this->info['download_content_length'];
    }
    
    /**
     * Gets the value of the 'upload_content_length' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function uploadlength() {
        return $this->info['upload_content_length'];
    }
    
    /**
     * Gets the value of the 'starttransfer_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function starttime() {
        return $this->info['starttransfer_time'];
    }
    
    /**
     * Gets the value of the 'redirect_time' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function redirecttime() {
        return $this->info['redirect_time'];
    }
    
    /**
     * Gets the value of the 'certinfo' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function certificate() {
        return $this->info['certinfo'];
    }
    
    /**
     * Gets the value of the 'request_header' piece of info
     *
     * @return mixed The value of the piece of info
     */
    public function requestheader() {
        return $this->info['request_header'];
    }
    
    /**
     * Gets the response header
     *
     * @return string The response's header
     */
    public function header() {
        return $this->header;
    }
    
    /**
     * Gets the response body
     *
     * @return string The response's body
     */
    public function body() {
        return $this->body;
    }
    
    /**
     * Converts the object to a string
     *
     * The string version of the response is the body, to allow the
     * object to act as a string
     *
     * @return string The body of the response
     */
    public function __toString() {
        return $this->body;
    }
}