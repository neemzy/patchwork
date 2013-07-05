<?php

define('DEBUG_MODE', true);
define('BASE_PATH', __DIR__);
define('MODEL_NAMESPACE', 'Pizza\\Model\\');
define('ADMIN_ROOT', 'pizza.list');

define('DB_HOST', 'localhost');
define('DB_NAME', 'pizza');
define('DB_USER', 'root');
define('DB_PASS', 'admin');

define('BO_USER', 'pizza');
define('BO_PASS', 'admin');

$translations = array(
    'messages' => array(
        'fr' => array(
            '[title]' => 'Titre',
            '[content]' => 'Contenu',
            'This value should not be blank.' => 'Ce champ est requis.',
            'This value is not valid.' => 'Ce champ est invalide.',
            'This value is not a valid email address.' => 'Ce champ doit contenir une adresse e-mail valide.'
        ),
    ),
);
