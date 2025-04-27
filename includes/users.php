<?php
// Gestion des routes pour les utilisateurs
function handleUsers($method, $request) {
    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                getUserById($request[1]);
            } else {
                getAllUsers();
            }
            break;
        case 'POST':
            addUser();
            break;
        case 'PUT':
            if (isset($request[1])) {
                updateUser($request[1]);
            }
            break;
        case 'DELETE':
            if (isset($request[1])) {
                deleteUser($request[1]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
}

// Retourne tous les utilisateurs
function getAllUsers() {
    $users = [];

    if (($handle = fopen(CSV_USERS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            $users[] = ['id' => (int)$data[0], 'name' => $data[1]];
        }
        fclose($handle);
    }

    echo json_encode($users);
}

// Retourne un utilisateur précis selon son ID
function getUserById($id) {
    if (($handle = fopen(CSV_USERS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                fclose($handle);
                echo json_encode(['id' => (int)$data[0], 'name' => $data[1]]);
                return;
            }
        }
        fclose($handle);
    }
    http_response_code(404);
    echo json_encode(['error' => 'Utilisateur introuvable']);
}

// Ajoute un nouvel utilisateur
function addUser() {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom manquant']);
        return;
    }

    if (!preg_match('/^[A-Z][a-z]{3,24}$/', $input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom invalide']);
        return;
    }

    $id = getNextId(CSV_USERS);
    $newUser = [$id, $input['name']];

    $handle = fopen(CSV_USERS, 'a');
    fputcsv($handle, $newUser);
    fclose($handle);

    http_response_code(201);
    echo json_encode(['message' => 'Utilisateur ajouté', 'id' => $id]);
}

// Met à jour un utilisateur existant
function updateUser($id) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom manquant']);
        return;
    }

    if (!preg_match('/^[A-Z][a-z]{3,24}$/', $input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nom invalide']);
        return;
    }

    $updated = false;
    $rows = [];

    if (($handle = fopen(CSV_USERS, 'r')) !== false) {
        while (($data = fgetcsv($handle))) {
            if ((int)$data[0] === (int)$id) {
                $data[1] = $input['name'];
                $updated = true;
            }
            $rows[] = $data;
        }
        fclose($handle);
    }

    if ($updated) {
        $handle = fopen(CSV_USERS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Utilisateur mis à jour']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur introuvable']);
    }
}

// Supprime un utilisateur
function deleteUser($id) {
    $deleted = false;
    $rows = [];

    if (($handle = fopen(CSV_USERS, 'r')) !== false) {
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
        $handle = fopen(CSV_USERS, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        echo json_encode(['message' => 'Utilisateur supprimé']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur introuvable']);
    }
}
?>
