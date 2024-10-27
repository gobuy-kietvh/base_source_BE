<?php

namespace App\Libs;

use Symfony\Component\Yaml\Yaml;

class ConfigUtil
{
    public const PATH = 'config/Constant/';

    public const VALUE_LIST_DIR = 'Values';

    public const MESSAGE_DIR = 'Messages';

    /**
     * Get root path
     * @return string
     */
    public static function rootPath()
    {
        return __DIR__ . '/../../';
    }

    /**
     * Get message from message_file, params is optional
     *
     * @param string $key
     * @param array $paramArray
     * @return mixed|null
     */
    public static function getMessage($key, $paramArray = [])
    {
        $message = self::getConfig(self::MESSAGE_DIR, $key);
        if ($message && is_string($message)) {
            foreach ($paramArray as $param => $value) {
                $message = str_replace(sprintf('<%d>', $param), $value, $message);
            }
        }

        return $message;
    }

    /**
     * Get $key value from value_list_file
     *
     * @param string $keys
     * @param array $options
     * @return array|null
     */
    public static function getValueList($keys, $options = [])
    {
        $keys = explode('.', $keys);
        if (! is_array($keys) || count($keys) != 2) {
            return null;
        }
        [$fileName, $param] = $keys;
        $valueList = self::loadValueList($fileName, $param);
        if ($valueList && is_array($valueList)) {
            $resultList = [];
            foreach ($valueList as $key => $value) {
                if (! is_array($value)) {
                    $value = explode('|', $value);
                    if (! isset($value[1])) {
                        $resultList[$key] = $value[0];
                    } elseif (isset($options['getList']) && $options['getList']) {
                        $resultList[$key] = $value[0];
                    }
                } else {
                    $resultList[$key] = $value;
                }
            }

            return $resultList;
        }

        return $valueList;
    }

    /**
     * Get value/text from const
     *
     * @param string $keys
     * @param bool $getText
     * @return int|string|null
     */
    public static function getValue($keys, $getText = false)
    {
        $keys = explode('.', $keys);
        if (! is_array($keys) || count($keys) != 3) {
            return null;
        }
        [$fileName, $key, $const] = $keys;
        $valueList = self::loadValueList($fileName, $key);
        if ($valueList && is_array($valueList)) {
            foreach ($valueList as $key => $value) {
                $value = explode('|', $value);
                if (isset($value[1]) && $value[1] == $const) {
                    if ($getText) {
                        return $value[0];
                    }

                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Load $key value from specific value_list_file
     *
     * @param $fileName
     * @param $key
     * @return mixed
     */
    public static function loadValueList($fileName, $key)
    {
        global $cacheYaml;
        global $cacheValueList;

        if (! isset($cacheYaml)) {
            $cacheYaml = [];
        }

        if (! isset($cacheValueList)) {
            $cacheValueList = [];
        }

        $valueListKey = $fileName . '.' . $key;
        if (isset($cacheValueList[$valueListKey])) {
            // retreiving from local static cache
            return $cacheValueList[$valueListKey];
        }

        if (isset($cacheYaml[$fileName])) {
            // retreiving from local static cache
            $paramValue = $cacheYaml[$fileName];
        } else {
            $filePath = self::rootPath() . self::PATH . self::VALUE_LIST_DIR . '/' . $fileName . '.yml';
            $paramValue = Yaml::parse(file_get_contents($filePath));
            $cacheYaml[$fileName] = $paramValue; // cache
        }

        $cacheValueList[$valueListKey] = $paramValue[$key]; // cache

        return $paramValue[$key];
    }

    /**
     * Get config params from DemoBundle/Reosurce/config/folder_name
     *
     * @param string $folderName
     * @param string $paramKey
     * @return null
     */
    private static function getConfig($folderName, $paramKey)
    {
        global $cacheConfig;
        global $cacheConfigFile;

        if (! isset($cacheConfig)) {
            $cacheConfig = [];
        }

        if (! isset($cacheConfigFile)) {
            $cacheConfigFile = [];
        }

        if (isset($cacheConfig[$paramKey])) {
            return $cacheConfig[$paramKey];
        }

        $folderPath = self::rootPath() . self::PATH . $folderName;
        $paramKeyArr = explode('.', $paramKey);

        foreach (glob($folderPath . '/*.yml') as $yamlSrc) {
            if (isset($cacheConfigFile[basename($yamlSrc)])) {
                $paramValue = $cacheConfigFile[basename($yamlSrc)];
            } else {
                $paramValue = Yaml::parse(file_get_contents($yamlSrc));
                $cacheConfigFile[basename($yamlSrc)] = $paramValue;
            }

            $found = true;
            foreach ($paramKeyArr as $key) {
                if (! isset($paramValue[$key])) {
                    $found = false;
                    break;
                }
                $paramValue = $paramValue[$key];
            }
            if ($found) {
                $cacheConfig[$paramKey] = $paramValue;

                return $paramValue;
            }
        }

        return null;
    }
}
