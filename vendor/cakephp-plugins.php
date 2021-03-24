<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'FOC/Authenticate' => $baseDir . '/vendor/friendsofcake/authenticate/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'RememberMe' => $baseDir . '/vendor/nojimage/cakephp-remember-me/',
        'WyriHaximus/TwigView' => $baseDir . '/vendor/wyrihaximus/twig-view/'
    ]
];