<?php
// Inclusion des fichiers nécessaires
require_once 'config.php';
require_once 'includes/pokemons.php';
require_once 'includes/users.php';
require_once 'includes/teams.php';

// Définir que toutes les réponses seront envoyées en JSON
header('Content-Type: application/json');

// Récupère l'URL demandée
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Enlève le préfixe '/API' de l'URL
$request = str_replace(API_PREFIX, '', $request);
$request = explode('/', trim($request, '/'));

// Routing : on dirige vers le bon gestionnaire en fonction de l'URL
switch ($request[0] ?? '') {
    case 'pokemons':
        handlePokemons($method, $request);
        break;
    case 'users':
        handleUsers($method, $request);
        break;
    case 'teams':
        handleTeams($method, $request);
        break;
    default:
        // Route non trouvée
        http_response_code(404);
        echo json_encode(['error' => 'Route non trouvée']);
}
?>
