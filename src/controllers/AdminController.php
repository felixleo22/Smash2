<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 24/09/2019
 * Time: 17:36
 */

namespace Smash\controllers;

use Smash\models\Admin;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class AdminController extends Controller {

  

    // public function listeAdmin(Request $request, Response $response) {
    //     if ($_SESSION['current_user'] == null) {
    //         return $response->withRedirect('/');
    //     } else {
    //         $admins = Admin::all();
    //     return $this->views->render($response, 'log.html.twig', ['admins' => $admins, 'session' => $_SESSION['current_user']]);
    // }
    // }

    public function formulaireEditAdmin(Request $request, Response $response, $args){
        $admin = Admin::find($request->getAttribute('login'));
        return $this->views->render($response, 'editAdmin.html.twig',['admin'=>$admin]);
    }

    public function verifAdmin(Request $request, Response $response, $args){

        $perso = [];
        $perso['login'] = $request->getParsedBodyParam('login');
        $perso['mdp'] = $request->getParsedBodyParam('mdp');
        $perso['super'] = $request->getParsedBodyParam('super');

        $admin = Admin::find(intval($_POST['login']));
        $admin->login = $perso['login'];
        $admin->mdp = $perso['mdp'];
        $admin->super = $perso['super'];
        $admin->save();

        return $response->withRedirect('/admin/liste');
    }


    /**
     * affiche le formulaire de creation d'un admin via un fichier twig
     * //TODO ne pas afficher le mot de passe en l'écrivant
     */
    public function formulaireCreation(Request $request, Response $response, $args){
        return $this->views->render($response, 'ajoutAdmin.html.twig');
        
    }

    public function creerAdmin(Request $request, Response $response, $args){
        //TODO filtrage dans la base de donnée
        $ad['login'] = Utils::getFilteredPost($request, 'login');
        $ad['mdp'] = Utils::getFilteredPost($request, 'mdp');
        $admin = Admin::create($ad);
        return Utils::redirect($response, 'accueil');
    }

    /**
     * selectionne tout les admins de la bdd et les affichent
     */
    public function listeAdmin(Request $request, Response $response, $args) {
        $listeAdmin = Admin::all();
        return $this->views->render($response, 'affichageAdmin.html.twig', ['admins' => $listeAdmin]);
    }

    /**
     * 
     */
    public function afficherAdmin(Request $request, Response $response, $args) {
        //TODO Verifier connexion de l'utilisateur
        $admin = Admin::find($request->getAttribute('login'));
        return $this->views->render($response, 'editAdmin.html.twig',['admins'=>$admin]);
    }

    /**
     * 
     */
    public function modiferAdmin(Request $request, Response $response, $args) {
        //TODO Verifier connexion de l'utilisateur
        $login = Utils::sanitize($args['login']);
        if($login === null) return Utils::redirect($request, 'formModifAdmin');
        $admin = Admin::find($login);
        $admin->login = Utils::getFilteredPost($request, "login");
        $admin->save();
        return Utils::redirect($response, 'afficherListeAdmins');
    }

    /**
     * 
     */
    public function suppressionAdmin(Request $request, Response $response, $args){
        //TODO Verifier connexion de l'utilisateur
        $login = Utils::sanitize($args['login']);
        $admin = Admin::find($login);
        if($admin != null) {
            $admin->delete();
        }
        return $response->withRedirect('/admin/liste');
    }

}
