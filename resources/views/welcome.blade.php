<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Publicador Telegram - Tabs con Token único</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />

    <style>
        #editor-container {
            height: 200px;
            margin-bottom: 1rem;
        }
        .boton-item {
            margin-bottom: 0.5rem;
        }
        .boton-item input {
            margin-right: 0.5rem;
            width: 200px;
        }
        button.agregar-boton {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="container my-4">
    <h1 class="mb-4">Publicador Telegram</h1>

    <!-- Token común para todos los formularios -->
    <div class="mb-4">
        <label for="token-global" class="form-label">Token de autorización (válido para todos los formularios):</label>
        <input type="password" class="form-control" id="token-global" placeholder="Ingresa tu token" />
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="tabsForm" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="telegram-tab" data-bs-toggle="tab" data-bs-target="#telegram" type="button" role="tab" aria-controls="telegram" aria-selected="true">Telegram</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="otro-tab" data-bs-toggle="tab" data-bs-target="#otro" type="button" role="tab" aria-controls="otro" aria-selected="false">Otro Formulario</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="false">Grupos</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content mt-3">
        <!-- Telegram Tab -->
        <div class="tab-pane fade show active" id="telegram" role="tabpanel" aria-labelledby="telegram-tab">
            <form id="formulario-telegram">
                <!-- Ya no hace falta el input token aquí -->
                <div class="mb-3">
                    <label for="editor-container" class="form-label">Texto (usa formato visual):</label>
                    <div id="editor-container"></div>
                    <!-- Campo oculto que se enviará con el texto limpio -->
                    <input type="hidden" name="texto" id="texto" />
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label">URL de la imagen (opcional):</label>
                    <input type="url" class="form-control" name="imagen" />
                </div>

                <h5>Botones (opcional)</h5>
                <div id="botones-container" class="mb-3">
                    <div class="boton-item d-flex gap-2 mb-2">
                        <input type="text" class="form-control" placeholder="Texto del botón" name="boton_text[]" />
                        <input type="url" class="form-control" placeholder="URL del botón" name="boton_url[]" />
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mb-3" onclick="agregarBoton()">Agregar otro botón</button>

                <br/>
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
        </div>

        <!-- Otro Formulario Tab -->
        <div class="tab-pane fade" id="otro" role="tabpanel" aria-labelledby="otro-tab">
            <form id="formulario-otro">
                <!-- Tampoco el token aquí -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ingresa el nombre" />
                </div>

                <div class="mb-3">
                    <label for="link" class="form-label">Link:</label>
                    <input type="url" class="form-control" id="link" name="link" required placeholder="Ingresa el link" />
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label">Tags (separados por coma):</label>
                    <input type="text" class="form-control" id="tags" name="tags" placeholder="ej: tag1, tag2, tag3" />
                </div>

                <button type="submit" class="btn btn-success">Enviar</button>
            </form>
        </div>

                <!-- Grupos Tab -->
        <div class="tab-pane fade" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
            <form id="formulario-grupo" class="mb-3">
                <div class="mb-3">
                    <label class="form-label">Chat ID:</label>
                    <input type="number" class="form-control" name="chat_id" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Título:</label>
                    <input type="text" class="form-control" name="title" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo:</label>
                    <select class="form-select" name="type" required>
                        <option value="group">Grupo</option>
                        <option value="channel">Canal</option>
                        <option value="user">Usuario</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Grupo</button>
            </form>

            <h5>Grupos registrados</h5>
            <ul id="lista-grupos" class="list-group"></ul>
        </div>

    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    // Inicializar Quill para editor Telegram
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                ['link'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });

    function cleanTelegramHTML(html) {
        html = html.replace(/<p>/gi, '').replace(/<\/p>/gi, '\n');
        html = html.replace(/<br\s*\/?>/gi, '\n');
        html = html.replace(/<ol>([\s\S]*?)<\/ol>/gi, (match, content) => {
            let items = content.match(/<li>(.*?)<\/li>/gi);
            if (!items) return '';
            return items.map((item, i) => `${i + 1}. ${item.replace(/<\/?li>/gi, '')}`).join('\n') + '\n';
        });
        html = html.replace(/<ul>([\s\S]*?)<\/ul>/gi, (match, content) => {
            let items = content.match(/<li>(.*?)<\/li>/gi);
            if (!items) return '';
            return items.map(item => `- ${item.replace(/<\/?li>/gi, '')}`).join('\n') + '\n';
        });
        // Remueve todas las etiquetas que no sean las permitidas en Telegram
        html = html.replace(/<(?!\/?(b|strong|i|em|u|s|strike|del|code|pre|a)(\s|>))/gi, '');
        return html;
    }

    function agregarBoton() {
        const contenedor = document.getElementById('botones-container');
        const div = document.createElement('div');
        div.classList.add('boton-item', 'd-flex', 'gap-2', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control" placeholder="Texto del botón" name="boton_text[]" />
            <input type="url" class="form-control" placeholder="URL del botón" name="boton_url[]" />
        `;
        contenedor.appendChild(div);
    }

    // Función para obtener el token global y validar
    function getTokenGlobal() {
        const token = document.getElementById('token-global').value.trim();
        if (!token) {
            alert('Por favor ingresa un token válido.');
            return null;
        }
        return token;
    }

    // Submit formulario Telegram
    document.getElementById('formulario-telegram').addEventListener('submit', async function(e) {
        e.preventDefault();

        const token = getTokenGlobal();
        if (!token) return;

        let html = quill.root.innerHTML;
        let cleanedHtml = cleanTelegramHTML(html);
        document.getElementById('texto').value = cleanedHtml;

        const formData = new FormData(e.target);

        const data = {
            texto: formData.get('texto'),
            imagen: formData.get('imagen') || null,
        };

        const textos = formData.getAll('boton_text[]');
        const urls = formData.getAll('boton_url[]');
        const botones = [];

        for (let i = 0; i < textos.length; i++) {
            const textoBoton = textos[i]?.trim();
            const urlBoton = urls[i]?.trim();
            if (textoBoton && urlBoton) {
                botones.push({ texto: textoBoton, url: urlBoton });
            }
        }

        if (botones.length > 0) {
            data.botones = botones;
        }

        const res = await fetch('/api/mensajes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();
        console.log(json);
        alert(json.message || 'Mensaje enviado');
    });

    // Submit formulario Otro
    document.getElementById('formulario-otro').addEventListener('submit', async function(e) {
        e.preventDefault();

        const token = getTokenGlobal();
        if (!token) return;

        const nombre = e.target.nombre.value.trim();
        const link = e.target.link.value.trim();
        const tags = e.target.tags.value.trim();

        if (!nombre || !link) {
            alert('Por favor completa los campos Nombre y Link.');
            return;
        }

        const data = { nombre, link };

        if (tags) {
            data.tags = tags.split(',').map(t => t.trim()).filter(t => t.length > 0); // array!
        }


        const res = await fetch('/api/publicar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();
        console.log(json);
        alert(json.message || 'Datos enviados');
    });

</script>
<script>
    async function cargarGrupos() {
        const token = getTokenGlobal();
        if (!token) return;

        const res = await fetch('/api/chats', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const json = await res.json();
        const lista = document.getElementById('lista-grupos');
        lista.innerHTML = '';

        json.forEach(chat => {
            const item = document.createElement('li');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            item.innerHTML = `
                <span><strong>${chat.title}</strong> (${chat.type}) — ID: ${chat.chat_id}</span>
                <button class="btn btn-sm btn-danger" onclick="eliminarGrupo(${chat.id})">Eliminar</button>
            `;
            lista.appendChild(item);
        });
    }

    async function eliminarGrupo(id) {
        const token = getTokenGlobal();
        if (!token) return;

        if (!confirm('¿Estás seguro de eliminar este grupo?')) return;

        const res = await fetch(`/api/chats/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const json = await res.json();
        alert(json.message || 'Grupo eliminado');
        await cargarGrupos();
    }

    document.getElementById('formulario-grupo').addEventListener('submit', async function (e) {
        e.preventDefault();

        const token = getTokenGlobal();
        if (!token) return;

        const formData = new FormData(e.target);
        const data = {
            chat_id: parseInt(formData.get('chat_id')),
            title: formData.get('title'),
            type: formData.get('type')
        };

        const res = await fetch('/api/chats', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });

        const json = await res.json();
        alert(json.message || 'Grupo agregado');
        e.target.reset();
        await cargarGrupos();
    });

    // Cargar grupos al cambiar al tab "Grupos"
    document.getElementById('grupos-tab').addEventListener('click', cargarGrupos);
</script>

</body>
</html>
