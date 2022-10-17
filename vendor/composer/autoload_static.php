<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit25202e753355db9c3a8911d9dcc7e2ba
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Granal1\\Php2\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Granal1\\Php2\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit25202e753355db9c3a8911d9dcc7e2ba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit25202e753355db9c3a8911d9dcc7e2ba::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit25202e753355db9c3a8911d9dcc7e2ba::$classMap;

        }, null, ClassLoader::class);
    }
}
