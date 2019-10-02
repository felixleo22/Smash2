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
        return $this->views->render($response, 'formAdmin.html.twig',['admin'=>$admin]);
    }

    /**
     * affiche le formulaire de creation d'un admin via un fichier twig
     * //TODO ne pas afficher le mot de passe en l'écrivant
     */
    public function formulaireCreation(Request $request, Response $response, $args){
        return $this->views->render($response, 'formAdmin.html.twig');
        
    }

    public function creerAdmin(Request $request, Response $response, $args){
        //TODO filtrage dans la base de donnée
        $login = Utils::getFilteredPost($request, 'login');
        $password = Utils::getFilteredPost($request, 'mdp');
        $admin = Auth::creerAdmin($login, $password);
        FlashMessage::flashSuccess('L\'admin a été créé !');
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
     * 
     */
    public function afficherAdmin(Request $request, Response $response, $args) {
        //TODO Verifier connexion de l'utilisateur
        $admin = Admin::find($request->getAttribute('id'));
        return $this->views->render($response, 'formAdmin.html.twig',['admins'=>$admin]);
    }

    /**
     * 
     */
    public function modifierAdmin(Request $request, Response $response, $args) {
        //TODO Verifier connexion de l'utilisateur
        $id = Utils::sanitize($args['id']);
        if($id === null) return Utils::redirect($request, 'listeAdmins');
        $admin = Admin::find($id);
         if($admin == null) {
            FlashMessage::flashError('Cet admin n\'existe pas !');
            return Utils::redirect($response, 'listeAdmins');
        }
        $admin->login = Utils::getFilteredPost($request, "login");
        $admin->save();

        FlashMessage::flashSuccess($admin->login.' a été modifié !');
        return Utils::redirect($response, 'listeAdmins');
    }

    //TODO modification du mdp

    /**
     * 
     */
    public function suppressionAdmin(Request $request, Response $response, $args){
        $id = Utils::sanitize($args['id']);
        $admin = Admin::find($id);
        if($admin == null) {
            FlashMessage::flashError('Cet admin n\'existe pas !');
            return Utils::redirect($response, 'listeAdmins');
        }
        $admin->delete();
        FlashMessage::flashSuccess($admin->login.' a été supprimé !');
        return Utils::redirect($response, 'listeAdmins');
    }

    public function afficherFomulaireConnexion(Request $request, Response $response, $args) {
        return $this->views->render($response, 'login.html.twig');
    }

    public function connecter(Request $request, Response $response, $args){
        $login = Utils::getFilteredPost($request,'login');
        $pwd = Utils::getFilteredPost($request, 'password');
        if(!Auth::connexion($login,$pwd)){
            FlashMessage::flashError('Login ou mot de passe incorrecte !');
            return Utils::redirect($response, 'formConnexion');
        }
        
        FlashMessage::flashSuccess('Vous êtes connecté en tant que '.$login);
        return Utils::redirect($response, 'accueil');
    }

    public function deconnecter(Request $request, Response $response){
        Auth::deconnexion(); 
        return Utils::redirect($response, 'accueil');
    }

}
