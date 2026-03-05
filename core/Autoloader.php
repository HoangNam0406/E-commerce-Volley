<?php
/**
 * Autoloader function
 */
function autoload($class) {
    // Convert class name to file path
    $paths = [
        'app/models/',
        'app/controllers/',
        'core/',
    ];

    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    die("Class $class not found");
}

spl_autoload_register('autoload');
?>
