<?php

namespace nullref\core\components;

use Dotenv;
use Yii;
use yii\helpers\Console;

/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */
class DotenvManger extends Dotenv
{
    public static function modify()
    {
        $data = self::load(Yii::getAlias('@app/..'));

        $result = implode(PHP_EOL, array_map(function ($item) {
            if (is_array($item)) {
                $default = $item[1];
                $name = $item[0];
                $value = Console::input("$name [$default] : ");
                if (empty($value)) {
                    $value = $default;
                }
                return $name . '=' . $value;
            }
            return $item;
        }, $data));

        file_put_contents(Yii::getAlias('@app/../.env'), $result);
    }

    public static function load($path, $file = '.env')
    {
        if (!is_string($file)) {
            $file = '.env';
        }

        $filePath = rtrim($path, '/') . '/' . $file;
        if (!is_readable($filePath) || !is_file($filePath)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Dotenv: Environment file %s not found or not readable. ' .
                    'Create file with your environment settings at %s',
                    $file,
                    $filePath
                )
            );
        }

        // Read file into an array of lines with auto-detected line endings
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        $data = [];

        foreach ($lines as $line) {
            // Disregard comments
            if (strpos(trim($line), '#') === 0) {
                $data[] = $line;
                continue;
            }
            // Only use non-empty lines that look like setters
            if (strpos($line, '=') !== false) {
                $tmp = static::normaliseEnvironmentVariable($line, null);
                $data[] = $tmp;
            }
        }

        return $data;
    }
}