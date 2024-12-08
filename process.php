<?php
header('Content-Type: application/json');
$dataFile = 'cantico.json'; // Caminho do arquivo JSON onde os dados serão armazenados

// Lê os dados enviados pelo frontend
$request = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $request['action'];
    $jsonData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

    if ($action === 'adicionar') {
        $novoItem = [
            "titulo" => $request['titulo'],
            "estrofes" => $request['estrofes'],
            "momento" => $request['momento'],
            "livro_numero" => $request['livro_numero'],
            "idioma" => $request['idioma']
        ];

        // Verifica se já existe um item com o mesmo título
        $index = array_search($novoItem['titulo'], array_column($jsonData, 'titulo'));
        if ($index !== false) {
            $jsonData[$index] = $novoItem; // Atualiza se já existe
        } else {
            $jsonData[] = $novoItem; // Adiciona novo
        }

        file_put_contents($dataFile, json_encode($jsonData, JSON_PRETTY_PRINT));
        echo json_encode(["message" => "Cântico salvo com sucesso!"]);
    } elseif ($action === 'remover') {
        $titulo = $request['titulo'];
        $jsonData = array_filter($jsonData, function ($item) use ($titulo) {
            return $item['titulo'] !== $titulo;
        });

        file_put_contents($dataFile, json_encode(array_values($jsonData), JSON_PRETTY_PRINT));
        echo json_encode(["message" => "Cântico removido com sucesso!"]);
    } elseif ($action === 'salvar') {
        file_put_contents($dataFile, json_encode($request['data'], JSON_PRETTY_PRINT));
        echo json_encode(["message" => "Dados atualizados com sucesso!"]);
    }
} else {
    echo json_encode(["message" => "Método não suportado."]);
}
?>
