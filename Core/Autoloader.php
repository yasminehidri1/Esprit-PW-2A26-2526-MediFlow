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
        // PSR-4 namespaced classes (Core\, Controllers\, Models\)
        spl_autoload_register(function ($class) {
            $prefixes = [
                'Core\\' => __DIR__ . DIRECTORY_SEPARATOR,
                'Controllers\\' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR,
                'Models\\' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR,
                'Services\\' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR,
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

        // Fallback: load non-namespaced classes from Controllers/ and Models/
        // (e.g. Product, Order, ProductController, OrderController, config)
        spl_autoload_register(function ($class) {
            // Skip namespaced classes — already handled above
            if (strpos($class, '\\') !== false) {
                return;
            }

            $searchDirs = [
                __DIR__ . '/../Models/',
                __DIR__ . '/../Controllers/',
                __DIR__ . '/../',
            ];

            foreach ($searchDirs as $dir) {
                $file = $dir . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
}
