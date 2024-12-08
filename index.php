<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Cânticos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f3f3;
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        textarea, input, button {
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            resize: none;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        button {
            background-color: #6a1b9a;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #4a148c;
        }
        .list-container {
            height: 300px; /* Altura fixa para a lista */
            overflow-y: auto; /* Ativa a rolagem vertical */
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
        .list-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .list-item:last-child {
            border-bottom: none;
        }
        .details-container {
            grid-column: span 2;
            height: 150px; /* Altura fixa para os detalhes */
            overflow-y: auto; /* Ativa a rolagem vertical */
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Coluna Esquerda -->
        <div>
            <div class="form-group">
                <input type="text" id="search" placeholder="Pesquisar...">
            </div>
            <div class="form-group">
                <textarea id="estrofes" rows="2" placeholder="Estrofes..."></textarea>
            </div>
            <div class="form-group">
                <input type="text" id="momento" placeholder="Momento...">
            </div>
            <div class="form-group">
                <input type="text" id="livroNumero" placeholder="Livro-Número...">
            </div>
            <div class="form-group">
                <input type="text" id="idioma" placeholder="Idioma..." value="Portugues">
            </div>
            <div class="actions">
                <button id="adicionar">Adicionar</button>
                <button id="remover">Remover</button>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div>
            <div class="list-container" id="listContainer">
                <!-- Os itens da lista serão carregados aqui -->
            </div>
        </div>

        <!-- Detalhes -->
        <div class="details-container" id="detailsContainer">
            Clique em um item para ver os detalhes.
        </div>
    </div>

    <script>
        const adicionarBtn = document.getElementById('adicionar');
        const removerBtn = document.getElementById('remover');
        const listContainer = document.getElementById('listContainer');
        const detailsContainer = document.getElementById('detailsContainer');
        const searchInput = document.getElementById('search');

        let localData = []; // Dados do JSON local
        let selectedItemIndex = null; // Index do item selecionado

        // Carregar dados do JSON local ao iniciar
        async function loadLocalData() {
            const response = await fetch('cantico.json'); // Caminho do JSON local
            if (response.ok) {
                localData = await response.json();
                updateList(); // Atualiza a lista de cânticos
            } else {
                console.error("Erro ao carregar o arquivo JSON.");
            }
        }

        // Atualiza a lista exibida no lado direito
        function updateList() {
            listContainer.innerHTML = '';
            // Ordena os dados por título
            localData.sort((a, b) => a.titulo.localeCompare(b.titulo));
            localData.forEach((item, index) => {
                const div = document.createElement('div');
                div.classList.add('list-item');
                div.textContent = item.titulo;
                div.addEventListener('click', () => {
                    selectedItemIndex = index;
                    detailsContainer.innerHTML = `
                        <strong>Título:</strong> ${item.titulo}<br>
                        <strong>Estrofes:</strong> ${item.estrofes}<br>
                        <strong>Momento:</strong> ${item.momento}<br>
                        <strong>Livro-Número:</strong> ${item.livro_numero}<br>
                        <strong>Idioma:</strong> ${item.idioma}
                    `;
                });
                listContainer.appendChild(div);
            });
        }

        // Função para adicionar ou editar um cântico
        adicionarBtn.addEventListener('click', () => {
            const titulo = searchInput.value.trim(); // Usa o campo de pesquisa como título
            if (!titulo) {
                alert("Por favor, insira um título válido!");
                return;
            }

            // Verifica se o título já existe
            const existsIndex = localData.findIndex(item => item.titulo.toLowerCase() === titulo.toLowerCase());

            const estrofes = document.getElementById('estrofes').value;
            const momento = document.getElementById('momento').value;
            const livroNumero = document.getElementById('livroNumero').value;
            const idioma = document.getElementById('idioma').value;

            const newItem = { titulo, estrofes, momento, livro_numero: livroNumero, idioma };

            if (existsIndex !== -1) {
                // Se o título existe, edita o item
                localData[existsIndex] = newItem;
                alert("Cântico editado com sucesso!");
            } else {
                // Caso contrário, adiciona como novo
                localData.push(newItem);
                alert("Cântico adicionado com sucesso!");
            }

            // Atualiza a lista após a alteração
            updateList();

            // Limpar os campos após adicionar ou editar
            searchInput.value = '';
            document.getElementById('estrofes').value = '';
            document.getElementById('momento').value = '';
            document.getElementById('livroNumero').value = '';
            document.getElementById('idioma').value = 'Portugues';

            // Atualizar o JSON com a nova lista
            fetch('process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'adicionar',
                    titulo,
                    estrofes,
                    momento,
                    livro_numero: livroNumero,
                    idioma
                })
            })
            .then(res => res.json())
            .then(data => console.log(data.message))
            .catch(err => console.error(err));
        });

        // Função para remover um cântico
        removerBtn.addEventListener('click', () => {
            if (selectedItemIndex === null) {
                alert("Por favor, selecione um cântico para remover.");
                return;
            }

            const confirmDelete = confirm("Tem certeza que deseja remover este cântico?");
            if (confirmDelete) {
                // Remove o item do JSON
                localData.splice(selectedItemIndex, 1);

                // Atualiza o JSON local
                fetch('process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'salvar',
                        data: localData
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert("Cântico removido com sucesso!");
                    // Atualiza a lista e limpa os detalhes
                    updateList();
                    detailsContainer.innerHTML = 'Clique em um item para ver os detalhes.';
                })
                .catch(err => console.error(err));
            }
        });

        // Carregar dados ao inicializar
        loadLocalData();

        // Pesquisa em tempo real
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            const filteredData = localData.filter(item => item.titulo.toLowerCase().includes(term));
            listContainer.innerHTML = '';
            filteredData.forEach(item => {
                const div = document.createElement('div');
                div.classList.add('list-item');
                div.textContent = item.titulo;
                div.addEventListener('click', () => {
                    selectedItemIndex = localData.indexOf(item);
                    detailsContainer.innerHTML = `
                        <strong>Título:</strong> ${item.titulo}<br>
                        <strong>Estrofes:</strong> ${item.estrofes}<br>
                        <strong>Momento:</strong> ${item.momento}<br>
                        <strong>Livro-Número:</strong> ${item.livro_numero}<br>
                        <strong>Idioma:</strong> ${item.idioma}
                    `;
                });
                listContainer.appendChild(div);
            });
        });
    </script>
</body>
</html>
