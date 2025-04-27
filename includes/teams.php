<?php
// Gestion des routes pour les équipes
function handleTeams($method, $request) {
    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                getTeamById($request[1]);
            } else {
                getAllTeams();
            }
            break;
        case 'POST':
            addTeam();
            break;
        case 'PUT':
            if (isset($request[1])) {
                updateTeam($request[1]);
            }
            break;
        case 'DELETE':
            if (isset($request[1])) {
                deleteTeam($request[1]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
}

// Retourne toutes les équipes
function getAllTeams() {
    $teams = [];

    if (($handle = fopen(CSV_TEAMS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            $teams[] = ['id' => (int)$data[0], 'user_id' => (int)$data[1], 'pokemon_id' => (int)$data[2]];
        }
        fclose($handle);
    }

    echo json_encode($teams);
}

// Retourne une équipe précise
function getTeamById($id) {
    if (($handle = fopen(CSV_TEAMS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                fclose($handle);
                echo json_encode(['id' => (int)$data[0], 'user_id' => (int)$data[1], 'pokemon_id' => (int)$data[2]]);
                return;
            }
        }
        fclose($handle);
    }
    http_response_code(404);
    echo json_encode(['error' => 'Equipe introuvable']);
}

// Ajoute une nouvelle équipe
function addTeam() {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['user_id']) || !isset($input['pokemon_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants']);
        return;
    }

    $id = getNextId(CSV_TEAMS);
    $newTeam = [$id, (int)$input['user_id'], (int)$input['pokemon_id']];

    $handle = fopen(CSV_TEAMS, 'a');
    fputcsv($handle, $newTeam);
    fclose($handle);

    http_response_code(201);
    echo json_encode(['message' => 'Equipe ajoutée', 'id' => $id]);
}

// Met à jour une équipe existante
function updateTeam($id) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['user_id']) || !isset($input['pokemon_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants']);
        return;
    }

    $updated = false;
    $rows = [];

    if (($handle = fopen(CSV_TEAMS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                $data[1] = (int)$input['user_id'];
                $data[2] = (int)$input['pokemon_id'];
                $updated = true;
            }
            $rows[] = $data;
        }
        fclose($handle);
    }

    if ($updated) {
        $handle = fopen(CSV_TEAMS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Equipe mise à jour']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Equipe introuvable']);
    }
}

// Supprime une équipe
function deleteTeam($id) {
    $deleted = false;
    $rows = [];

    if (($handle = fopen(CSV_TEAMS, 'r')) !== false) {
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
        $handle = fopen(CSV_TEAMS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Equipe supprimée']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Equipe introuvable']);
    }
}
?>
