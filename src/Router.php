<?php
// src/Router.php
require_once __DIR__ . '/../config.php';
class Router {
    private $method;
    private $uri;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function dispatch() {
        // Supprimer le préfixe
        $path = substr($this->uri, strlen(API_PREFIX));
        $segments = array_values(array_filter(explode('/', $path)));

        // Routes pokemons
        if (isset($segments[0]) && $segments[0] === 'pokemons') {
            require_once __DIR__ . '/PokemonController.php';
            $ctrl = new PokemonController();
            if ($this->method === 'GET' && count($segments) === 1) {
                $ctrl->getAll();
            } elseif ($this->method === 'GET' && count($segments) === 2) {
                $ctrl->getOne((int)$segments[1]);
            } elseif ($this->method === 'POST' && count($segments) === 1) {
                $ctrl->create();
            } elseif ($this->method === 'PUT' && count($segments) === 2) {
                $ctrl->update((int)$segments[1]);
            } elseif ($this->method === 'DELETE' && count($segments) === 2) {
                $ctrl->delete((int)$segments[1]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Route non trouvée']);
            }
            return;
        }

        // Routes users et associations
        if (isset($segments[0]) && $segments[0] === 'users') {
            require_once __DIR__ . '/UserController.php';
            $ctrl = new UserController();
            if ($this->method === 'GET' && count($segments) === 1) {
                $ctrl->getAll();
            } elseif ($this->method === 'GET' && count($segments) === 2) {
                $ctrl->getOne((int)$segments[1]);
            } elseif ($this->method === 'POST' && count($segments) === 1) {
                $ctrl->create();
            } elseif ($this->method === 'PATCH' && count($segments) === 3 && $segments[2] === 'password') {
                $ctrl->updatePassword((int)$segments[1]);
            } elseif ($this->method === 'DELETE' && count($segments) === 2) {
                $ctrl->delete((int)$segments[1]);
            } elseif ($this->method === 'POST' && count($segments) === 4 && $segments[2] === 'pokemons') {
                $ctrl->addPokemonToUser((int)$segments[1], (int)$segments[3]);
            } elseif ($this->method === 'DELETE' && count($segments) === 4 && $segments[2] === 'pokemons') {
                $ctrl->removePokemonFromUser((int)$segments[1], (int)$segments[3]);
            } elseif ($this->method === 'GET' && count($segments) === 3 && $segments[2] === 'pokemons') {
                $ctrl->getUserPokemons((int)$segments[1]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Route non trouvée']);
            }
            return;
        }

        http_response_code(404);
        echo json_encode(['message' => 'Route non trouvée']);
    }
}
