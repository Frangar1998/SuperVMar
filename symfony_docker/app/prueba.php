<?php

use App\Entity\TipoImpositivo;
use App\Entity\Producto;

try {
    $db = new PDO('mysql:host=database;port=3306;dbname=symfony_docker', "root", "secret");
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . PHP_EOL;
}

$sql = "SELECT * FROM symfony_docker.tipo_impositivo";

