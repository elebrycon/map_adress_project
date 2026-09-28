<?php

$envPath = dirname(__DIR__) . '/map_adress_project-private/.env';

if (!is_readable($envPath)) {
    throw new RuntimeException('The private configuration file is missing or unreadable.');
}

$config = parse_ini_file($envPath, false, INI_SCANNER_RAW);

if ($config === false) {
    throw new RuntimeException('Unable to read the private configuration.');
}

$requiredSettings = [
    'GOOGLE_MAPS_API_KEY',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD'
];

foreach ($requiredSettings as $setting) {
    if (!isset($config[$setting]) || !is_string($config[$setting])) {
        throw new RuntimeException('Missing or invalid configuration setting: ' . $setting);
    }

    if ($setting !== 'DB_PASSWORD' && trim($config[$setting]) === '') {
        throw new RuntimeException('Empty configuration setting: ' . $setting);
    }
}

return $config;