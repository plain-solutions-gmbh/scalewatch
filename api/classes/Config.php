<?php

require_once(__DIR__ . '/../autoload.php');
use Symfony\Component\Yaml\Yaml;

class Config
{

    static ?array $config = null;
    static ?array $routines = null;
    static ?array $params = null;
    static ?array $tanks = null;

    public static function init($config)
    {
        static::$config ??= $config;
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists('Config', $name)) {
            return static::$$name($arguments);
        }

        if (array_key_exists($name, static::$config)) {
            return static::$config[$name];
        }

        throw new Exception("unable to get config for $name");
        
    }

    public static function tanks(array $carry = null)
    {
        if (static::$tanks === null) {
            static::$tanks = [];
            static::collectTanks(static::plant('scopes'));
        }
        return static::$tanks;
    }

    public static function collectTanks($data)
    {

        foreach ($data as $item) {

            if (\array_key_exists('tank', $item) && in_array($item['tank'], static::$tanks) === false) {
                array_push(static::$tanks, $item['tank']);
            }

            if (array_key_exists('rows', $item)) {
                static::collectTanks($item['rows']);
            }

            if (array_key_exists('cols', $item)) {
                static::collectTanks($item['cols']);
            }

        }


    }

    public static function plant($filename)
    {
        $plant_folder = static::location('plant');
        return Yaml::parseFile("$plant_folder/$filename.yml");
    }

    public static function routines()
    {

        if (static::$routines !== null) {
            return static::$routines;
        }

        static::$routines = [];
        $routine_folder = static::location('routines');

        foreach (scandir($routine_folder) as $file) {

            $file = $routine_folder . $file;

            if (pathinfo($file, PATHINFO_EXTENSION) !== 'csv') {
                continue;
            }

            $name = ucfirst(pathinfo($file, PATHINFO_FILENAME));

            static::$routines[$name] = $file;

        }

        return static::$routines;
    }

    public static function get(?string $key = null)
    {
        if ($key !== null) {
            return static::$config[$key];
        }

        return [
            'routines'  => static::routines(),
            'users'     => static::plant('users'),
            'params'    => static::plant('params')
        ];

    }

    public static function location(?string $key = null) {

        $root = static::$config['locations']['root'] . '/';

        if ($key === null || $key === 'root') {
            return $root;
        }

        $subfolder = static::$config['locations'][$key] ?? 'fake_folder';

        if (file_exists($location = $root . $subfolder . '/')) {
            return $location;
        }

        throw new Exception("Location for $key not found");
        
    }

}
