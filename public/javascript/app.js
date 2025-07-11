document.addEventListener('DOMContentLoaded', () => {
    // Die URL zu Ihrer API. Passen Sie diese bei Bedarf an.
    const apiUrl = '/Api/api.php';

    // Eindeutige Session-ID erstellen oder aus dem sessionStorage abrufen
    let sessionId = sessionStorage.getItem('tasksSessionId');
    if (!sessionId) {
        sessionId = crypto.randomUUID();
        sessionStorage.setItem('tasksSessionId', sessionId);
    }

    // Header-Objekt für alle API-Anfragen
    const apiHeaders = {
        'Content-Type': 'application/json',
        'X-Session-ID': sessionId
    };

    // Referenzen zu den DOM-Elementen
    const form = document.getElementById('task-form');
    const formContainer = document.getElementById('form-container');
    const taskList = document.getElementById('task-list');
    const taskIdInput = document.getElementById('task-id');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const submitButton = document.getElementById('submit-button');
    const loadingIndicator = document.getElementById('loading-indicator');

    // ----- DATENLADEN (GET) -----
    // Lädt alle Aufgaben von der API und zeigt sie in der Liste an.
    const fetchTasks = async () => {
        loadingIndicator.style.display = 'block';
        taskList.innerHTML = ''; // Leert die Liste vor dem Neuladen
        taskList.appendChild(loadingIndicator);

        try {
            const response = await fetch(apiUrl, {
                headers: { 'X-Session-ID': sessionId } // Nur Session-ID für GET
            });

            const contentType = response.headers.get("content-type");
            if (!response.ok || !contentType || !contentType.includes("application/json")) {
                const errorText = await response.text();
                throw new Error(`Server-Antwort ist kein gültiges JSON. Antwort: ${errorText}`);
            }

            const tasks = await response.json();

            // Überprüfen, ob die Antwort ein Array ist.
            if (!Array.isArray(tasks)) {
                console.error('Empfangene Daten sind kein Array:', tasks);
                throw new Error('Unerwartetes Datenformat vom Server erhalten. Erwartet wurde ein Array.');
            }

            loadingIndicator.style.display = 'none';
            if (tasks.length === 0) {
                taskList.innerHTML = '<li class="text-center text-gray-500">Keine Aufgaben gefunden.</li>';
            } else {
                tasks.forEach(task => {
                    const li = createTaskElement(task);
                    taskList.appendChild(li);
                });
            }
        } catch (error) {
            loadingIndicator.style.display = 'none';
            taskList.innerHTML = `<li class="text-center text-red-500 p-4 bg-red-50 rounded-lg">Fehler: ${escapeHTML(error.message)}</li>`;
            console.error('Fehler beim Abrufen der Aufgaben:', error);
        }
    };

    // Erstellt ein Listenelement für eine einzelne Aufgabe
    const createTaskElement = (task) => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200';
        li.dataset.id = task.id;

        li.innerHTML = `
            <div class="flex items-center flex-grow min-w-0">
                <input type="checkbox" class="task-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer flex-shrink-0" ${task.completed ? 'checked' : ''}>
                <div class="ml-4 min-w-0">
                    <span class="task-title font-medium text-gray-800 block break-words ${task.completed ? 'completed' : ''}">${escapeHTML(task.title)}</span>
                    <p class="task-description text-sm text-gray-600 block break-words ${task.completed ? 'completed' : ''}">${escapeHTML(task.description || '')}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 flex-shrink-0 ml-4">
                <button class="update-btn text-gray-400 hover:text-blue-600 transition duration-150" aria-label="Aktualisieren">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </button>
                <button class="delete-btn text-gray-400 hover:text-red-600 transition duration-150" aria-label="Löschen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </div>
        `;
        return li;
    };


    // ----- FORMULAR-HANDHABUNG (CREATE/UPDATE) -----
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = taskIdInput.value;
        const title = titleInput.value;
        const description = descriptionInput.value;

        const taskData = {
            title,
            description
        };
        const isUpdating = !!id;

        // Wenn wir aktualisieren, fügen wir die ID zum Request-Body hinzu.
        if (isUpdating) {
            taskData.id = id;
        }

        const url = isUpdating ? `${apiUrl}?id=${id}` : apiUrl;
        const method = isUpdating ? 'PATCH' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: apiHeaders,
                body: JSON.stringify(taskData)
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Fehler beim Speichern. Status: ${response.status}. Server-Antwort: ${errorText}`);
            }

            resetForm();
            await fetchTasks();
        } catch (error) {
            console.error('Fehler beim Speichern der Aufgabe:', error);
            alert(`Fehler beim Speichern der Aufgabe: ${error.message}`);
        }
    });

    // ----- LISTEN-INTERAKTIONEN (DELETE/UPDATE/TOGGLE) -----
    taskList.addEventListener('click', async (e) => {
        const target = e.target.closest('button, input[type="checkbox"]');
        if (!target) return;

        const li = target.closest('li');
        if (!li || !li.dataset.id) return;

        const id = li.dataset.id;

        // DELETE (DELETE)
        if (target.classList.contains('delete-btn')) {
            try {
                const response = await fetch(`${apiUrl}?id=${id}`, {
                    method: 'DELETE',
                    headers: { 'X-Session-ID': sessionId }
                });
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Status: ${response.status}. Server-Antwort: ${errorText}`);
                }
                li.remove();
            } catch (error) {
                console.error('Fehler beim Löschen:', error);
                alert(`Aufgabe konnte nicht gelöscht werden: ${error.message}`);
            }
        }

        // UPDATE-MODUS STARTEN
        if (target.classList.contains('update-btn')) {
            const title = li.querySelector('.task-title').textContent;
            const description = li.querySelector('.task-description').textContent;

            titleInput.value = title;
            descriptionInput.value = description;
            taskIdInput.value = id;
            submitButton.textContent = 'Aktualisieren';
            submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            submitButton.classList.add('bg-green-600', 'hover:bg-green-700');

            formContainer.scrollIntoView({ behavior: 'smooth' });
        }

        // TOGGLE COMPLETION (PATCH)
        if (target.classList.contains('task-checkbox')) {
            const isCompleted = target.checked;
            try {
                const response = await fetch(`${apiUrl}?id=${id}`, {
                    method: 'PATCH',
                    headers: apiHeaders,
                    body: JSON.stringify({ completed: isCompleted })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Status: ${response.status}. Server-Antwort: ${errorText}`);
                }

                li.querySelector('.task-title').classList.toggle('completed', isCompleted);
                li.querySelector('.task-description').classList.toggle('completed', isCompleted);
            } catch (error) {
                console.error('Fehler beim Aktualisieren des Status:', error);
                alert(`Status konnte nicht aktualisiert werden: ${error.message}`);
                target.checked = !isCompleted;
            }
        }
    });

    // ----- HILFSFUNKTIONEN -----
    const resetForm = () => {
        form.reset();
        taskIdInput.value = '';
        submitButton.textContent = 'Senden';
        submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
    };

    const escapeHTML = (str) => {
        if (typeof str !== 'string') return '';
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    };

    // Initiales Laden der Aufgaben
    fetchTasks();
});
