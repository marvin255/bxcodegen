<?php

return [
    'paths' => [
        'tests' => 'tests',
    ],
    'actor_suffix' => 'Tester',
    'settings' => [
        'bootstrap' => '_bootstrap.php',
    ],
    'extensions' => [
        'enabled' => [
            0 => 'Codeception\Extension\RunFailed',
        ],
    ],
];
