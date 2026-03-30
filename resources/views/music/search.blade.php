<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador Rythme</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-2xl border border-gray-700">
        <h1 class="text-3xl font-bold mb-6 text-center text-green-500">Spotify Search</h1>

        <div class="flex gap-2 mb-8">
            <input type="text" id="search-input" placeholder="Canción o artista..."
                class="flex-1 p-3 rounded-lg bg-gray-700 border-none focus:ring-2 focus:ring-green-500 text-white">
            <button id="search-button" class="bg-green-500 hover:bg-green-600 text-black font-bold py-3 px-6 rounded-lg transition">
                Buscar
            </button>
        </div>

        <div id="loading" class="hidden text-center py-4">
            <div class="animate-spin inline-block w-8 h-8 border-4 border-green-500 border-t-transparent rounded-full"></div>
        </div>

        <div id="results" class="space-y-4"></div>

        <div id="message" class="mt-6 p-4 rounded-lg hidden text-center font-semibold"></div>
    </div>

    <script>
        const btnSearch = document.getElementById('search-button');
        const inputSearch = document.getElementById('search-input');
        const resultsDiv = document.getElementById('results');
        const loading = document.getElementById('loading');
        const messageBox = document.getElementById('message');

        function showMsg(text, isError = false) {
            messageBox.textContent = text;
            messageBox.className = `mt-6 p-4 rounded-lg text-center font-semibold ${isError ? 'bg-red-900 text-red-200' : 'bg-green-900 text-green-200'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 4000);
        }

        btnSearch.addEventListener('click', async () => {
            const query = inputSearch.value.trim();
            if(!query) return showMsg('Escribe algo...', true);

            resultsDiv.innerHTML = '';
            loading.classList.remove('hidden');

            try {
                const url = "{{ route('api.music.spotify.search') }}?query=" + encodeURIComponent(query);
                const resp = await fetch(url);
                const data = await resp.json();

                if(!resp.ok) throw new Error(data.error || 'Error desconocido');

                data.forEach(track => {
                    const card = document.createElement('div');
                    card.className = "flex items-center gap-4 bg-gray-700 p-4 rounded-lg hover:bg-gray-600 transition";
                    card.innerHTML = `
                        <img src="${track.cover_url}" class="w-16 h-16 rounded shadow-lg">
                        <div class="flex-1">
                            <p class="font-bold text-lg">${track.title}</p>
                            <p class="text-gray-400 text-sm">${track.artist}</p>
                        </div>
                        <button onclick="saveTrack('${track.spotify_id}', this)" class="bg-green-500 text-black text-xs font-bold py-2 px-4 rounded hover:bg-white transition">
                            GUARDAR
                        </button>
                    `;
                    resultsDiv.appendChild(card);
                });
            } catch (err) {
                showMsg(err.message, true);
            } finally {
                loading.classList.add('hidden');
            }
        });

        async function saveTrack(id, btn) {
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const resp = await fetch("{{ route('api.music.save-track') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ track_id: id })
                });
                const res = await resp.json();
                showMsg(res.message || res.error, !resp.ok);
                if(resp.ok) btn.textContent = 'LISTO';
            } catch (e) {
                showMsg('Fallo de red', true);
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
