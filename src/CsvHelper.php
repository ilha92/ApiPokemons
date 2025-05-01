<?php
// src/CsvHelper.php
class CsvHelper {
    private $file;
    private $data;

    public function __construct(string $filePath) {
        $this->file = $filePath;
        $this->load();
    }

    private function load() {
        $this->data = [];
        if (!file_exists($this->file)) return;
        if (($handle = fopen($this->file, 'r')) !== false) {
            $headers = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $this->data[] = array_combine($headers, $row);
            }
            fclose($handle);
        }
    }

    public function all(): array {
        return $this->data;
    }

    public function find(callable $callback) {
        foreach ($this->data as $item) {
            if ($callback($item)) return $item;
        }
        return null;
    }

    public function saveAll(): void {
        $handle = fopen($this->file, 'w');
        if (empty($this->data)) { fclose($handle); return; }
        fputcsv($handle, array_keys($this->data[0]));
        foreach ($this->data as $row) fputcsv($handle, $row);
        fclose($handle);
    }

    public function add(array $item): void {
        $this->data[] = $item;
        $this->saveAll();
    }

    public function update(callable $callback, array $newData): bool {
        foreach ($this->data as &$row) {
            if ($callback($row)) {
                $row = array_merge($row, $newData);
                $this->saveAll();
                return true;
            }
        }
        return false;
    }

    public function delete(callable $callback): bool {
        foreach ($this->data as $i => $row) {
            if ($callback($row)) {
                array_splice($this->data, $i, 1);
                $this->saveAll();
                return true;
            }
        }
        return false;
    }
}
