<?php

/**
 * use this file to configure Walrus deploy
 */

// Blacklist
$_ENV['W']['deploy']['blacklist'] = array(
    DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'testing',
    '.git',
    '.idea',
    '.editorconfig',
    '.gitattributes',
    'VHOST.md',
    '.DS_Store',
    'Thumbs.db',
    'cache',
    'config',
    'logs',
    'Test',
    'tusk',
);
