<?php
$file = 'cantico.json';

// Carrega os dados do arquivo JSON
function loadData() {
    global $file;
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

// Salva os dados no arquivo JSON
function saveData($data) {
    global $file;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Processa as requisições
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['action'])) {
    echo json_encode(['message' => 'Ação inválida.']);
    exit;
}

$action = $input['action'];
$data = loadData();

if ($action === 'adicionar') {
    $data[] = [
        'momento' => $input['momento'] ?? '',
        'livro_numero' => $input['livro_numero'] ?? '',
        'idioma' => $input['idioma'] ?? 'Portugues',
        'titulo' => $input['titulo'] ?? '',
        'estrofes' => $input['estrofes'] ?? ''
    ];
    saveData($data);
    echo json_encode(['message' => 'Cântico adicionado com sucesso.']);
} elseif ($action === 'remover') {
    // Remover o item baseado no índice
    $index = $input['index'] ?? null;
    if ($index !== null && isset($data[$index])) {
        array_splice($data, $index, 1); // Remove o item no índice fornecido
        saveData($data);
        echo json_encode(['message' => 'Cântico removido com sucesso.']);
    } else {
        echo json_encode(['message' => 'Item não encontrado.']);
    }
} elseif ($action === 'listar') {
    echo json_encode($data);
} else {
    echo json_encode(['message' => 'Ação desconhecida.']);
}
