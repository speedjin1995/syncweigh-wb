<?php
/**
 * Bootstrap file - autoloader for the App namespace (php/classes/)
 */
spl_autoload_register(function ($class) {
    $prefix = 'App\\';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $file = __DIR__ . '/classes/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
