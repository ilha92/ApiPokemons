<?php
// src/UserController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/CsvHelper.php';

class UserController {
    private $csvUsers;
    private $csvLinks;
    private $csvPokemons;

    public function __construct() {
        $this->csvUsers    = new CsvHelper(FILE_USERS);
        $this->csvLinks    = new CsvHelper(FILE_USER_POKEMONS);
        $this->csvPokemons = new CsvHelper(FILE_POKEMONS);
        header('Content-Type: application/json');
    }

    public function getAll() {
        echo json_encode($this->csvUsers->all());
    }

    public function getOne(int $id) {
        // TODO: implémenter
    }

    public function create() {
        // TODO: validation et ajout
    }

    public function updatePassword(int $id) {
        // TODO: mise à jour mot de passe
    }

    public function delete(int $id) {
        // TODO: suppression
    }

    public function addPokemonToUser(int $uid, int $pid) {
        $user = $this->csvUsers->find(fn($r)=> (int)$r['id']=== $uid);
        $pokemon = $this->csvPokemons->find(fn($r)=> (int)$r['id']=== $pid);
        if (!$user || !$pokemon) {
            http_response_code(404);
            echo json_encode(['message'=>'Utilisateur ou Pokémon non trouvé']);
            return;
        }
        $exists = $this->csvLinks->find(fn($r)=> (int)$r['user_id']===$uid && (int)$r['pokemon_id']===$pid);
        if ($exists) {
            http_response_code(400);
            echo json_encode(['message'=>'Pokémon déjà dans l\'équipe']);
            return;
        }
        $team = array_filter($this->csvLinks->all(), fn($r)=> (int)$r['user_id']=== $uid);
        if ((int)$user['age'] < 18 && count($team) >= 4) {
            http_response_code(400);
            echo json_encode(['message'=>'Limite de 4 Pokémons pour les mineurs']);
            return;
        }
        $this->csvLinks->add(['user_id'=>$uid, 'pokemon_id'=>$pid]);
        http_response_code(201);
        echo json_encode(['message'=>'Pokémon ajouté']);
    }

    public function removePokemonFromUser(int $uid, int $pid) {
        $deleted = $this->csvLinks->delete(fn($r)=> (int)$r['user_id']=== $uid && (int)$r['pokemon_id']=== $pid);
        if ($deleted) http_response_code(204);
        else {
            http_response_code(404);
            echo json_encode(['message'=>'Association non trouvée']);
        }
    }

    public function getUserPokemons(int $uid) {
        $user = $this->csvUsers->find(fn($r)=> (int)$r['id']=== $uid);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['message'=>'Utilisateur non trouvé']);
            return;
        }
        $links = array_filter($this->csvLinks->all(), fn($r)=> (int)$r['user_id']=== $uid);
        $result = [];
        foreach ($links as $l) {
            $p = $this->csvPokemons->find(fn($r)=> (int)$r['id']=== (int)$l['pokemon_id']);
            if ($p) $result[] = $p;
        }
        echo json_encode(array_values($result));
    }
}
