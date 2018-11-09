<?php

namespace AmoCRM\Models;

/**
 * Class Cache
 */

class Cache
{
    private $cache_path = '';
    private $expire = null;

    function __construct()
    {
        $this->cache_path = defined('CACHE_DIR')?CACHE_DIR:sys_get_temp_dir();
        $this->expire = defined('CACHE_EXPIRE')?CACHE_EXPIRE:'3600';
    }

    function cache(){

    }

    function setCache($name,$data){
        $this->writeFile($name,json_encode($data));
        return $data;
    }

    function getCache($name, $expire=null){
        if(!is_null($expire)){
            $this->expire = $expire;
        }

        $cached = $this->readFile($name);

        if($cached !== false){
            $data = json_decode($cached, true);
            $cache_time = $data['server_time'];
            $now_time = time();
            if($now_time >= $cache_time && $now_time <= ($cache_time + $this->expire)){
                return $data;
            }
        }
        return false;
    }

    function writeFile($name, $content){
        try {
            if(!is_dir($this->cache_path)) {
                mkdir($this->cache_path);
            }
            return file_put_contents($this->cache_path."/".$name,$content);
        } catch (Exception $e) {
            // Handle exception
            return false;
        }
    }

    function readFile($name){
        try {
            $content = file_get_contents($this->cache_path."/".$name, false);
            if ($content === false) {
                // Handle the error
            } else {
                return $content;
            }
        } catch (Exception $e) {
            // Handle exception
            return false;
        }
    }
}
