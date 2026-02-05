<?php
use Pecee\SimpleRouter\SimpleRouter;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use pkremer\WebFrontend\Twig\SvgExtension;

require_once __DIR__ . "/../vendor/autoload.php";

// Twig initialisieren
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => false, // dev: false, prod: __DIR__ . '/../var/cache/twig'
    'debug' => true,
]);
$twig->addExtension(new SvgExtension(realpath(__DIR__ . '/..')));
$manifest = json_decode(file_get_contents(__DIR__ . '/../html/assets/manifest.json'), true);
$vars = ['manifest' => $manifest];

SimpleRouter::get('/', function() use ($vars, $twig) {
    return $twig->render('index.twig', $vars + [
    ]);
});
SimpleRouter::get('/about', function() use ($vars, $twig) {
    return $twig->render('about.twig', $vars + [
    ]);
});
SimpleRouter::get('/homelab', function() use ($vars, $twig) {
    return $twig->render('homelab.twig', $vars + [
    ]);
});
SimpleRouter::get('/apps', function() use ($vars, $twig) {
    return $twig->render('apps.twig', $vars + [
    ]);
});
SimpleRouter::get('/devops', function() use ($vars, $twig) {
    return $twig->render('devops.twig', $vars + []);
});
SimpleRouter::get('/imprint', function() use ($vars, $twig) {
    return $twig->render('imprint.twig', $vars + [
    ]);
});
SimpleRouter::get('/privacy', function() use ($vars, $twig) {
    return $twig->render('privacy.twig', $vars + [
    ]);
});

try {
    SimpleRouter::start();
} catch (NotFoundHttpException) {
    http_response_code(404);
    echo $twig->render('404.twig', $vars + []);
} catch (Throwable) {
    http_response_code(500);
    echo $twig->render('500.twig', $vars + []);
}
