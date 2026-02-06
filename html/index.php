<?php
require_once __DIR__ . "/../vendor/autoload.php";

use pkremer\WebFrontend\ElementFactory;
use pkremer\WebFrontend\PageParser;
use pkremer\WebFrontend\Validation\ElementNormalizer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use pkremer\WebFrontend\Twig\SvgExtension;
use pkremer\WebFrontend\Render\RenderContext;
use pkremer\WebFrontend\Render\Renderer;
use pkremer\WebFrontend\Inline\InlineParser;
use Twig\Extension\DebugExtension;

$env = getenv('APP_ENV') ?: 'prod';
$debug = ($env === 'dev');
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => $env === 'prod' ? __DIR__ . '/../var/cache/twig' : false, // dev: false, prod: __DIR__ . '/../var/cache/twig'
    'debug' => $debug,
]);
$twig->addExtension(new SvgExtension(realpath(__DIR__ . '/..')));
if ($debug) {
    $twig->addExtension(new DebugExtension());
}
$manifest = json_decode(file_get_contents(__DIR__ . '/../html/assets/manifest.json'), true);
$vars = ['_manifest' => $manifest];

try {
    $routes = yaml_parse_file(__DIR__ . '/../content/routes.yaml');
    foreach ($routes as $route) {
        if ($route['route'] === $_SERVER['REQUEST_URI']) {
            $parser = new PageParser();
            $ast = $parser->parse(file_get_contents(__DIR__ . "/../content/{$route['page']}.page"));

            $factory = new ElementFactory();
            $page = $factory->create($ast);

            $normalizer = new ElementNormalizer();
            $normalizer->normalize($page);

            $context = new RenderContext($twig, $vars);
            $renderer = new Renderer($context, new InlineParser());
            echo $renderer->render($page);
            exit;
        }
    }

    http_response_code(404);
    echo $twig->render('404.twig', $vars + []);
} catch (Throwable $e) {


    http_response_code(500);
    echo $twig->render('500.twig', $vars + [
        'exception' => $e,
        'app_env' => $env,
        'app_debug' => $debug,
    ]);
}