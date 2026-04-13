<?php
/**
 * PSR-4 Autoloader
 * 
 * Automatically loads classes based on their namespace
 * 
 * @package MediFlow\Core
 * @version 1.0.0
 */

namespace Core;

class Autoloader
{
    /**
     * Register the autoloader
     * 
     * @return void
     */
    public static function register(): void
    {
        spl_autoload_register(function ($class) {
            $prefixes = [
                'Core\\' => __DIR__ . DIRECTORY_SEPARATOR,
                'Controllers\\' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR,
                'Models\\' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR,
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                if (strpos($class, $prefix) !== 0) {
                    continue;
                }

                $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
                $file = $baseDir . $relative;

                if (file_exists($file)) {
                    require $file;
                }

                return;
            }
        });
    }
}
