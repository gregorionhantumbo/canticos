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
            grid-template-columns: 350px 420px 420px;
            /* Larguras fixas para as 3 colunas */
            gap: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            max-width: 1300px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #itemCount {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        textarea,
        input,
        button {
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
            height: 420px;
            /* Altura fixa para a lista */
            overflow-y: auto;
            /* Ativa a rolagem vertical */
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
            height: 455px;
            /* Altura fixa para os detalhes */
            overflow-y: auto;
            /* Ativa a rolagem vertical */
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
        <!-- Coluna Esquerda: Lista de Cânticos -->
        <div>
            <div class="form-group">
                <input type="text" id="search" placeholder="Pesquisar...">
            </div>
            <div class="list-container" id="listContainer">
                <!-- A lista de cânticos será carregada aqui -->
            </div>
            <div class="form-group">
                <label id="itemCount">Total de cânticos: 0</label>
            </div>
        </div>

        <!-- Coluna do Meio: Campos de Edição -->
        <div>
            <div class="form-group">
                <textarea id="estrofes" rows="16" placeholder="Estrofes..."></textarea>
            </div>
            <div class="form-group">
                <input type="text" id="momento" placeholder="Momento...">
            </div>
            <div class="form-group">
                <input type="text" id="livroNumero" placeholder="Livro-Número..." value="Oremos-">
            </div>
            <div class="form-group">
                <input type="text" id="idioma" placeholder="Idioma..." value="Portugues">
            </div>
            <div class="actions">
                <button id="adicionar">Adicionar</button>
                <button id="editar">Editar</button>
                <button id="salvar">Salvar</button>
                <button id="remover">Remover</button>
            </div>
        </div>

        <!-- Coluna Direita: Detalhes do Item Selecionado -->
        <div>
            <div class="details-container" id="detailsContainer">
                Clique em um item para ver os detalhes.
            </div>
        </div>
    </div>

    <script>
        const adicionarBtn = document.getElementById('adicionar');
        const editarBtn = document.getElementById('editar');
        const salvarBtn = document.getElementById('salvar');
        const removerBtn = document.getElementById('remover');
        const listContainer = document.getElementById('listContainer');
        const detailsContainer = document.getElementById('detailsContainer');
        const searchInput = document.getElementById('search');

        let localData = []; // Dados do JSON local
        let selectedItemIndex = null; // Índice do item selecionado

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

        // Função de filtro
        searchInput.addEventListener('input', () => {
            const searchQuery = searchInput.value.trim().toUpperCase(); // Converte a busca para uppercase

            const filteredData = localData.filter(item =>
                item.titulo.toUpperCase().includes(searchQuery) // Compara com títulos em uppercase
            );

            updateList(filteredData); // Atualiza a lista com os itens filtrados
        });


        // Atualiza a lista de cânticos exibida no frontend
        function updateList(data = localData) {
            listContainer.innerHTML = ''; // Limpa a lista atual

            // Ordena os dados por título
            data.sort((a, b) => a.titulo.localeCompare(b.titulo));

            // Atualiza a contagem de itens
            document.getElementById('itemCount').textContent = `Total de cânticos: ${data.length}`;

            // Adiciona cada item da lista à interface
            data.forEach((item, index) => {
                const div = document.createElement('div');
                div.classList.add('list-item');
                div.textContent = item.titulo;
                div.addEventListener('click', () => {
                    selectedItemIndex = index;
                    // Exibe os detalhes do item selecionado
                    detailsContainer.innerHTML = `
                <strong>Título:</strong> ${item.titulo}<br>
                <strong>Estrofes:</strong><br><pre>${item.estrofes}</pre><br>
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
            let titulo = searchInput.value.trim(); // Usa o campo de pesquisa como título
            if (!titulo) {
                alert("Por favor, insira um título válido!");
                return;
            }
            titulo = titulo.toUpperCase(); // Converte o título para uppercase

            // Verifica se o título já existe
            const existsIndex = localData.findIndex(item => item.titulo.toLowerCase() === titulo.toLowerCase());

            const estrofes = document.getElementById('estrofes').value;
            const momento = document.getElementById('momento').value;
            const livroNumero = document.getElementById('livroNumero').value;
            const idioma = document.getElementById('idioma').value;

            const newItem = {
                titulo,
                estrofes,
                momento,
                livro_numero: livroNumero,
                idioma
            };

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
            clearInputs();

            // Atualizar o JSON com a nova lista
            fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
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
            //titulo = titulo.toUpperCase(); // Converte o título para uppercase

        });

        // Função para limpar os campos de entrada
        function clearInputs() {
            searchInput.value = ''; // Limpar campo de pesquisa
            document.getElementById('estrofes').value = ''; // Limpar campo de estrofes
            document.getElementById('momento').value = ''; // Limpar campo de momento
            document.getElementById('livroNumero').value = 'Oremos-'; // Limpar campo de livro-número
            document.getElementById('idioma').value = 'Portugues'; // Limpar campo de idioma
        }


        // Editar o cântico
        editarBtn.addEventListener('click', () => {
            if (selectedItemIndex === null) {
                alert("Por favor, selecione um cântico para editar.");
                return;
            }

            const item = localData[selectedItemIndex];
            searchInput.value = item.titulo;
            document.getElementById('estrofes').value = item.estrofes;
            document.getElementById('momento').value = item.momento;
            document.getElementById('livroNumero').value = item.livro_numero;
            document.getElementById('idioma').value = item.idioma;
        });

        // Salvar cântico editado
        salvarBtn.addEventListener('click', () => {
            if (selectedItemIndex === null) {
                alert("Nenhum cântico foi selecionado para salvar.");
                return;
            }


            const titulo = searchInput.value.trim();
            const estrofes = document.getElementById('estrofes').value;
            const momento = document.getElementById('momento').value;
            const livroNumero = document.getElementById('livroNumero').value;
            const idioma = document.getElementById('idioma').value;

            clearInputs();

            fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
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
                .then(data => {
                    alert(data.message);
                    loadLocalData(); // Recarrega a lista
                })
                .catch(err => console.error(err));
        });

        // Remover cântico
        removerBtn.addEventListener('click', () => {
            if (selectedItemIndex === null) {
                alert("Por favor, selecione um cântico para remover.");
                return;
            }

            const titulo = localData[selectedItemIndex].titulo;

            fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'remover',
                        titulo
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    loadLocalData(); // Recarrega a lista
                })
                .catch(err => console.error(err));
        });

        // Carregar dados ao iniciar
        loadLocalData();
    </script>
</body>

</html>