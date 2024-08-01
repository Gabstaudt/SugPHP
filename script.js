document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('data-form');
    const tableBody = document.getElementById('view-table-body');
    const addPointBtn = document.getElementById('add-point-btn');
    const modal = document.getElementById('add-point-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const pointForm = document.getElementById('add-point-form');
    let map;
// inicializando o mapa
    function initMap() {
        map = L.map('map').setView([-23.5505, -46.6333], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }
// função de adicionar marcadores no mapa
    function addMarker(lat, lng, description) {
        L.marker([lat, lng]).addTo(map)
            .bindPopup(description)
            .openPopup();
    }

    initMap();
// carregar dados de monitoramento
    async function loadMonitoringData() {
        try {
            const response = await fetch('/read.php');
            const data = await response.json();

            tableBody.innerHTML = '';
            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.description}</td>
                    <td>${item.volume}</td>
                    <td>${item.latitude}</td>
                    <td>${item.longitude}</td>
                    <td>
                        <button onclick="editData(${item.id})">Editar</button>
                        <button onclick="deleteData(${item.id})">Excluir</button>
                    </td>
                `;
                tableBody.appendChild(row);

                addMarker(item.latitude, item.longitude, item.description);
            });
        } catch (error) {
            console.error('Erro ao carregar dados de monitoramento:', error);
        }
    }
// mnipulação do forulário de dados
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const id = document.getElementById('id').value;
        const description = document.getElementById('description').value;
        const volume = document.getElementById('volume').value;
        const latitude = parseFloat(document.getElementById('latitude').value);
        const longitude = parseFloat(document.getElementById('longitude').value);

        const formData = {
            id,
            description,
            volume,
            latitude,
            longitude
        };

        try {
            const response = await fetch('/save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('Dados salvos com sucesso!');
                loadMonitoringData();
                form.reset();
            } else {
                alert('Erro ao salvar dados.');
            }
        } catch (error) {
            console.error('Erro ao salvar dados:', error);
        }
    });
// edição de dados (finalizar)
    window.editData = function(id) {
        fetch(`/read.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id').value = data.id;
                document.getElementById('description').value = data.description;
                document.getElementById('volume').value = data.volume;
                document.getElementById('latitude').value = data.latitude;
                document.getElementById('longitude').value = data.longitude;
                map.setView([data.latitude, data.longitude], 12);
            })
            .catch(error => console.error('Erro ao buscar dados para edição:', error));
    };
//exclusão de dados
    window.deleteData = function(id) {
        if (confirm('Tem certeza de que deseja excluir este item?')) {
            fetch('/delete.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Dados excluídos com sucesso!');
                    loadMonitoringData();
                } else {
                    alert('Erro ao excluir dados.');
                }
            })
            .catch(error => console.error('Erro ao excluir dados:', error));
        }
    };
// manipulação modal
    addPointBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    pointForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const name = document.getElementById('point-name').value;
        const description = document.getElementById('point-description').value;
        const latitude = parseFloat(document.getElementById('point-latitude').value);
        const longitude = parseFloat(document.getElementById('point-longitude').value);

        // Adiciona o marcador ao mapa
        addMarker(latitude, longitude, description);

        // Fecha o modal e limpa o formulário
        modal.style.display = 'none';
        pointForm.reset();
    });

    loadMonitoringData();
});
