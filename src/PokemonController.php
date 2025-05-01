<?php
// src/PokemonController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/CsvHelper.php';

class PokemonController {
    private $csv;

    public function __construct() {
        $this->csv = new CsvHelper(FILE_POKEMONS);
        header('Content-Type: application/json');
    }

    public function getAll() {
        $all = $this->csv->all();
        if (isset($_GET['page'])) {
            $page = max(1, (int)$_GET['page']);
            $perPage = 10;
            $start = ($page - 1) * $perPage;
            $all = array_slice($all, $start, $perPage);
        }
        echo json_encode($all);
    }

    public function getOne(int $id) {
        $item = $this->csv->find(fn($r) => (int)$r['id'] === $id);
        if ($item) echo json_encode($item);
        else { http_response_code(404); echo json_encode(['message'=>'Pokemon non trouvé']); }
    }

    public function create() {
        $body = json_decode(file_get_contents('php://input'), true);
        // TODO: validation et ajout
    }

    public function update(int $id) {
        $body = json_decode(file_get_contents('php://input'), true);
        // TODO: validation et mise à jour
    }

    public function delete(int $id) {
        $deleted = $this->csv->delete(fn($r) => (int)$r['id']=== $id);
        if ($deleted) http_response_code(204);
        else { http_response_code(404); echo json_encode(['message'=>'Pokemon non trouvé']); }
    }
}
