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

    public function formulaireEditAdmin(Request $request, Response $response, $args){
        $admin = Admin::find($request->getAttribute('id'));
        return $this->views->render($response, 'editAdmin.html.twig',['admin'=>$admin]);
    }

    /**
     * affiche le formulaire de creation d'un admin via un fichier twig
     */
    public function formulaireCreation(Request $request, Response $response, $args){
        return $this->views->render($response, 'ajoutAdmin.html.twig');
        
    }

    /**
     * creation de d'un admin
     * verification des logins
     */
    public function creerAdmin(Request $request, Response $response, $args){
        //TODO filtrage dans la base de donnée
        $login = Utils::getFilteredPost($request, 'login');
        if(!Auth::loginDisponible($login)){
            FlashMessage::flashError('login deja utilisé');
            return Utils::redirect($response, 'formCreerAdmin', ['id' => $admin->id]);   
        }
        $password = Utils::getFilteredPost($request, 'mdp');
        $admin = Auth::creerAdmin($login, $password);
        return Utils::redirect($response, 'listeAdmins');
    }

    /**
     * selectionne tout les admins de la bdd et les affichent
     */
    public function listeAdmin(Request $request, Response $response, $args) {
        $listeAdmin = Admin::all();
        return $this->views->render($response, 'affichageAdmin.html.twig', ['admins' => $listeAdmin]);
    }

    /**
     * fenetre d'edition admin
     */
    public function afficherAdmin(Request $request, Response $response, $args) {
        $admin = Admin::find($request->getAttribute('id'));
        return $this->views->render($response, 'editAdmin.html.twig',['admins'=>$admin]);
    }

    /**
     * enrigistrement dans la base de donner des modifications de l'admins
     * verification des logins
     */
    public function modifierAdmin(Request $request, Response $response, $args) {
        $id = Utils::sanitize($args['id']);
        if($id === null) return Utils::redirect($request, 'listeAdmins');
        $admin = Admin::find($id);
        $admin->login = Utils::getFilteredPost($request, "login");
        if(Auth::loginDisponible($admin->login)){
            FlashMessage::flashError('login deja utilisé');
            return Utils::redirect($response, 'formModifAdmin',['id' => $admin->id]);   
        }
        $admin->save();
        return Utils::redirect($response, 'listeAdmins');
    }

    //TODO modification du mdp

    /**
     * supprime un admin dans la base de donnee
     * verification si suppresion du super admin
     */
    public function suppressionAdmin(Request $request, Response $response, $args){
        //TODO Verifier connexion de l'utilisateur
        $id = Utils::sanitize($args['id']);
        $admin = Admin::find($id);
        if($admin === null || $admin->super === 1) {
            FlashMessage::flashError('Impossible de supprimer cette utilisateur');
            return Utils::redirect($response, 'listeAdmins');
        }
        $admin->delete();
        return Utils::redirect($response, 'listeAdmins');
    }

    public function afficherFomulaireConnexion(Request $request, Response $response, $args) {
        return $this->views->render($response, 'login.html.twig');
    }

    /**
     * execute la connexion
     */
    public function connecter(Request $request, Response $response, $args){
        $login = Utils::getFilteredPost($request,'login');
        $pwd = Utils::getFilteredPost($request, 'password');
        if(!Auth::connexion($login,$pwd)){
            return Utils::redirect($response, 'formConnexion');
        }
        
        return Utils::redirect($response, 'accueil');
    }

    public function deconnecter(Request $request, Response $response){
        Auth::deconnexion(); 
        return Utils::redirect($response, 'accueil');
    }

    

}
