// app.js - Front-end JavaScript avec fetch

const API_URL = "http://localhost/api/todos";

// Charger toutes les todos au démarrage
document.addEventListener("DOMContentLoaded", () => {
  loadTodos();

  // Gestionnaire du formulaire
  document.getElementById("todoForm").addEventListener("submit", handleSubmit);
});

// Fonction pour charger toutes les todos (GET)
async function loadTodos() {
  try {
    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error("Erreur lors du chargement des todos");
    }

    const todos = await response.json();
    displayTodos(todos);
  } catch (error) {
    console.error("Erreur:", error);
    document.getElementById("todosContainer").innerHTML =
      '<div class="empty-state">❌ Erreur lors du chargement des tâches</div>';
  }
}

// Afficher les todos dans le DOM
function displayTodos(todos) {
  const container = document.getElementById("todosContainer");

  if (todos.length === 0) {
    container.innerHTML =
      '<div class="empty-state">Aucune tâche pour le moment 🎉</div>';
    return;
  }

  container.innerHTML = '<div class="todos-list"></div>';
  const todosList = container.querySelector(".todos-list");

  todos.forEach((todo) => {
    const todoElement = createTodoElement(todo);
    todosList.appendChild(todoElement);
  });
}

// Créer l'élément HTML d'une todo
function createTodoElement(todo) {
  const div = document.createElement("div");
  div.className = `todo-item ${todo.completed ? "completed" : ""}`;
  div.dataset.id = todo.id;

  const statusClass = todo.completed ? "status-completed" : "status-pending";
  const statusText = todo.completed ? "Terminée" : "En cours";

  div.innerHTML = `
        <div class="todo-header">
            <div class="todo-title">${escapeHtml(todo.title)}</div>
            <span class="status-badge ${statusClass}">${statusText}</span>
        </div>
        ${todo.description ? `<div class="todo-description">${escapeHtml(todo.description)}</div>` : ""}
        <div class="todo-actions">
            <button class="btn btn-success" onclick="toggleComplete(${todo.id}, ${!todo.completed})">
                ${todo.completed ? "↩️ Réactiver" : "✓ Terminer"}
            </button>
            <button class="btn btn-edit" onclick="editTodo(${todo.id})">✏️ Modifier</button>
            <button class="btn btn-danger" onclick="deleteTodo(${todo.id})">🗑️ Supprimer</button>
        </div>
        <div class="todo-date">Créée le ${formatDate(todo.created_at)}</div>
    `;

  return div;
}

// Créer une nouvelle todo (POST)
async function handleSubmit(e) {
  e.preventDefault();

  const title = document.getElementById("title").value;
  const description = document.getElementById("description").value;

  const todoData = {
    title: title,
    description: description,
    completed: 0,
  };

  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(todoData),
    });

    if (!response.ok) {
      throw new Error("Erreur lors de la création de la todo");
    }

    // Réinitialiser le formulaire
    document.getElementById("todoForm").reset();

    // Recharger la liste
    loadTodos();

    alert("✅ Tâche créée avec succès !");
  } catch (error) {
    console.error("Erreur:", error);
    alert("❌ Erreur lors de la création de la tâche");
  }
}

// Basculer l'état de complétion (PUT)
async function toggleComplete(id, completed) {
  try {
    // Récupérer d'abord la todo pour avoir toutes ses données
    const getResponse = await fetch(`${API_URL}/${id}`);
    const todo = await getResponse.json();

    // Mettre à jour avec le nouveau statut
    const response = await fetch(`${API_URL}/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        title: todo.title,
        description: todo.description,
        completed: completed ? 1 : 0,
      }),
    });

    if (!response.ok) {
      throw new Error("Erreur lors de la mise à jour");
    }

    loadTodos();
  } catch (error) {
    console.error("Erreur:", error);
    alert("❌ Erreur lors de la mise à jour de la tâche");
  }
}

// Modifier une todo (PUT)
async function editTodo(id) {
  try {
    // Récupérer la todo existante
    const response = await fetch(`${API_URL}/${id}`);
    const todo = await response.json();

    const newTitle = prompt("Nouveau titre:", todo.title);
    if (newTitle === null) return; // Annulation

    const newDescription = prompt("Nouvelle description:", todo.description);
    if (newDescription === null) return; // Annulation

    const updateResponse = await fetch(`${API_URL}/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        title: newTitle,
        description: newDescription,
        completed: todo.completed ? 1 : 0,
      }),
    });

    if (!updateResponse.ok) {
      throw new Error("Erreur lors de la modification");
    }

    loadTodos();
    alert("✅ Tâche modifiée avec succès !");
  } catch (error) {
    console.error("Erreur:", error);
    alert("❌ Erreur lors de la modification de la tâche");
  }
}

// Supprimer une todo (DELETE)
async function deleteTodo(id) {
  if (!confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
    return;
  }

  try {
    const response = await fetch(`${API_URL}/${id}`, {
      method: "DELETE",
    });

    if (!response.ok) {
      throw new Error("Erreur lors de la suppression");
    }

    loadTodos();
    alert("✅ Tâche supprimée avec succès !");
  } catch (error) {
    console.error("Erreur:", error);
    alert("❌ Erreur lors de la suppression de la tâche");
  }
}

// Utilitaires
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
