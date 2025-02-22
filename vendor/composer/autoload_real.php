<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitb2b0c5f75bcc12ad36642c58f6caf4cf
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitb2b0c5f75bcc12ad36642c58f6caf4cf', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitb2b0c5f75bcc12ad36642c58f6caf4cf', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitb2b0c5f75bcc12ad36642c58f6caf4cf::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
