<?php
namespace Moxo\Helpers;

class DataStore {
    const SESSION_NAME = 'DataStore';
    private static $helper;

    private function __construct() {} //I'ts a static class

    public static function init() {
        $helper = SessionHelper::getInstance();
        if ( ! $helper->checkSession(self::SESSION_NAME) )
            $helper->createSession(self::SESSION_NAME, []);

        self::$helper = $helper;

    }

    public static function set($key, $data){
        self::init();

        self::$helper->createOnSession(self::SESSION_NAME, $key, $data);
    }

    public static function get($key) {
        self::init();

        $data = self::$helper->selectSession(self::SESSION_NAME);
        if ( isset($data[$key]) )
            return $data[$key];

        return null;
    }


}
