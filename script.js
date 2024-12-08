const adicionarBtn = document.getElementById('adicionar');
const salvarBtn = document.getElementById('salvar');
const listContainer = document.getElementById('listContainer');
const detailsContainer = document.getElementById('detailsContainer');
const searchInput = document.getElementById('search');

let localData = [];

// Carrega os dados do JSON local ao iniciar
async function loadLocalData() {
    const response = await fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'listar' })
    });
    if (response.ok) {
        localData = await response.json();
        updateList();
    } else {
        console.error("Erro ao carregar os dados.");
    }
}

// Atualiza a lista exibida no lado direito
function updateList() {
    listContainer.innerHTML = '';
    localData.forEach((item) => {
        const div = document.createElement('div');
        div.classList.add('list-item');
        div.textContent = item.titulo;
        div.addEventListener('click', () => {
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

// Adiciona um novo cântico
adicionarBtn.addEventListener('click', () => {
    const titulo = searchInput.value.trim();
    if (!titulo) {
        alert("Por favor, insira um título válido!");
        return;
    }

    const estrofes = document.getElementById('estrofes').value;
    const momento = document.getElementById('momento').value;
    const livroNumero = document.getElementById('livroNumero').value;
    const idioma = document.getElementById('idioma').value;

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
    .then(data => {
        alert(data.message);
        loadLocalData(); // Atualiza a lista após adicionar
        searchInput.value = '';
        document.getElementById('estrofes').value = '';
        document.getElementById('momento').value = '';
        document.getElementById('livroNumero').value = '';
        document.getElementById('idioma').value = 'Portugues';
    });
});

// Salvar os cânticos
salvarBtn.addEventListener('click', () => {
    fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'salvar' })
    })
    .then(res => res.json())
    .then(data => alert(data.message))
    .catch(err => console.error(err));
});

// Carrega os dados ao abrir a página
loadLocalData();
