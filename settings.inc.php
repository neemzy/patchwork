<?php

// Environment
define('DEBUG_MODE', true);

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'pizza');
define('DB_USER', 'root');
define('DB_PASS', 'admin');

// Back-office
define('BO_USER', 'pizza');
define('BO_PASS', 'admin');

// Translations
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
