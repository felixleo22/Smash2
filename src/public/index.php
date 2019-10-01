<?php
session_start();
require_once('../../vendor/autoload.php');
require_once('../config/config.inc.php');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;

//Middlewares
use Smash\middlewares\AuthMiddleware;

//Controleurs
use Smash\controllers\IndexController;
use Smash\controllers\EntiteController; 
use Smash\controllers\AdminController;

$container["settings"] = $config;

//Installation de twig
$container['view'] = function($container) {
    $view = new \Slim\Views\Twig('../views', []);

    //Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    //ajout des fonctions perso pour twig
    $functionsArray = require_once('../config/twigFunctions.inc.php');
    foreach ($functionsArray as $fonction) {
        $view->getEnvironment()->addFunction($fonction);
    }

    return $view;
};


//Eloquent
$capsule = new Capsule;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function ($container) use ($capsule){
    return $capsule;
};

$app = new Slim\App($container);


/** Routes */

//affichage de la page d'accueil
$app->get('/', IndexController::class.':index') -> setName('accueil');

//gestion de la connexion
$app->get('/connexion', AdminController::class.':afficherFomulaireConnexion')->setName('formConnexion');
$app->post('/connexion', AdminController::class.':connecter')->setName('execConnexion');
$app->get('/deconnexion', AdminController::class.':deconnecter')->setName('execDeconnexion')->add(new AuthMiddleware());

//gestion des entites
$app->group('/entite', function($app) {
    $app->get('/creer', EntiteController::class.':formulaireCreation')->setName('formCreerEntite');
    $app->post('/creer', EntiteController::class.':creerEntite')->setName('execCreerEntite');
    
    $app->get('/liste', EntiteController::class.':listeEntite')->setname('listeEntites');

    $app->get('/modifier/{id}', EntiteController::class.':afficherEntite')->setname('formModifEntite');
    //TODO remplacer post par put
    $app->post('/modifier/{id}', EntiteController::class.':modifierEntite')->setName('execModifEntite');
    //TODO remplacer get par delete
    $app->get('/supprimer/{id}', EntiteController::class.':suppressionEntite')->setName('execSupprEntite');
})->add(new AuthMiddleware());

//gestion des admins
$app->group('/admin', function($app) {
    $app->get('/liste', AdminController::class.':listeAdmin')->setName('listeAdmins');

    $app->get('/creer', AdminController::class.':formulaireCreation')->setName('formCreerAdmin');
    $app->post('/creer', AdminController::class.':creerAdmin')->setName('exeCreerAdmin');

    //TODO uniformiser soit login soit id
    //TODO remplacer post par put
    $app->get('/modifier/{id}', AdminController::class.':formulaireEditAdmin')->setname('formModifAdmin');
    $app->post('/modifier/{id}', AdminController::class.':modifierAdmin')->setName('execModifAdmin');
    //TODO remplacer get par delete
    $app->get('/supprimer/{id}', AdminController::class.':suppressionAdmin')->setName('execSupprAdmin');
})->add(new AuthMiddleware());

/** Lancement de l'application */
$app->run();
