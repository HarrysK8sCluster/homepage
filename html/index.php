<?php
use Pecee\SimpleRouter\SimpleRouter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . "/../vendor/autoload.php";

// Twig initialisieren
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => false, // dev: false, prod: __DIR__ . '/../var/cache/twig'
    'debug' => true,
]);

SimpleRouter::get('/', function() use ($twig) {
    return $twig->render('index.twig', [
    ]);
});
SimpleRouter::get('/about', function() use ($twig) {
    return $twig->render('about.twig', [
    ]);
});
SimpleRouter::get('/homelab', function() use ($twig) {
    return $twig->render('homelab.twig', [
    ]);
});
SimpleRouter::get('/apps', function() use ($twig) {
    return $twig->render('apps.twig', [
    ]);
});
SimpleRouter::get('/ops', function() use ($twig) {
    return $twig->render('ops.twig', [
    ]);
});
SimpleRouter::get('/deepdive', function() use ($twig) {
    return $twig->render('deepdive.twig', [
    ]);
});
SimpleRouter::get('/imprint', function() use ($twig) {
    return $twig->render('imprint.twig', [
    ]);
});

SimpleRouter::start();