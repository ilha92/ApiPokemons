<?php
// Gestion des routes pour les pokémons
function handlePokemons($method, $request) {
    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                getPokemonById($request[1]);
            } else {
                getAllPokemons();
            }
            break;
        case 'POST':
            addPokemon();
            break;
        case 'PUT':
            if (isset($request[1])) {
                updatePokemon($request[1]);
            }
            break;
        case 'DELETE':
            if (isset($request[1])) {
                deletePokemon($request[1]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
}

// Retourne la liste de tous les pokémons
function getAllPokemons() {
    $pokemons = [];

    if (($handle = fopen(CSV_POKEMONS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            $pokemons[] = ['id' => (int)$data[0], 'name' => $data[1], 'size' => (float)$data[2]];
        }
        fclose($handle);
    }

    echo json_encode($pokemons);
}

// Retourne un pokémon précis selon son ID
function getPokemonById($id) {
    if (($handle = fopen(CSV_POKEMONS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                fclose($handle);
                echo json_encode(['id' => (int)$data[0], 'name' => $data[1], 'size' => (float)$data[2]]);
                return;
            }
        }
        fclose($handle);
    }
    http_response_code(404);
    echo json_encode(['error' => 'Pokemon introuvable']);
}

// Ajoute un nouveau pokémon
function addPokemon() {
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifie la présence des champs
    if (!isset($input['name']) || !isset($input['size'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants']);
        return;
    }

    // Valide le format du nom
    if (!preg_match('/^[A-Z][a-z]{3,24}$/', $input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom invalide']);
        return;
    }

    $id = getNextId(CSV_POKEMONS);
    $newPokemon = [$id, $input['name'], (float)$input['size']];

    // J'ajoute le nouveau pokémon dans le fichier CSV
    $handle = fopen(CSV_POKEMONS, 'a');
    fputcsv($handle, $newPokemon);
    fclose($handle);

    http_response_code(201);
    echo json_encode(['message' => 'Pokemon ajouté', 'id' => $id]);
}

// Met à jour un pokémon existant
function updatePokemon($id) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name']) || !isset($input['size'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants']);
        return;
    }

    if (!preg_match('/^[A-Z][a-z]{3,24}$/', $input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom invalide']);
        return;
    }

    $updated = false;
    $rows = [];

    // Charge tous les pokémons et met à jour le bon
    if (($handle = fopen(CSV_POKEMONS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                $data[1] = $input['name'];
                $data[2] = (float)$input['size'];
                $updated = true;
            }
            $rows[] = $data;
        }
        fclose($handle);
    }

    if ($updated) {
        // Réécrit tout le fichier CSV avec la modification
        $handle = fopen(CSV_POKEMONS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Pokemon mis à jour']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pokemon introuvable']);
    }
}

// Supprime un pokémon
function deletePokemon($id) {
    $deleted = false;
    $rows = [];

    if (($handle = fopen(CSV_POKEMONS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] !== (int)$id) {
                $rows[] = $data;
            } else {
                $deleted = true;
            }
        }
        fclose($handle);
    }

    if ($deleted) {
        // Réécrit le CSV sans le pokémon supprimé
        $handle = fopen(CSV_POKEMONS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Pokemon supprimé']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pokemon introuvable']);
    }
}

// Récupère le prochain ID disponible pour un nouveau pokémon
function getNextId($file) {
    $maxId = 0;
    if (($handle = fopen($file, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] > $maxId) {
                $maxId = (int)$data[0];
            }
        }
        fclose($handle);
    }
    return $maxId + 1;
}
?>
