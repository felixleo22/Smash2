<?php
namespace Smash\middlewares;

use Smash\controllers\FlashMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Middleware permettant la création et l'affichage de messages flash
 */
class FlashMiddleware {

    public function __invoke(Request $request, Response $response, $next) {
        $response = $next($request, $response);
        FlashMessage::middleware();
        return $response;
    }
}