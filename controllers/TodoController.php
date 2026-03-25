<?php
// controllers/TodoController.php - Contrôleur Todo

class TodoController
{
    private $db;
    private $todo;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->todo = new Todo($this->db);
    }

    // POST - Créer une nouvelle todo
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->title)) {
            $this->todo->title = $data->title;
            $this->todo->description = $data->description ?? '';
            $this->todo->completed = $data->completed ?? 0;

            if ($this->todo->create()) {
                http_response_code(201);
                echo json_encode([
                    'message' => 'Todo created',
                    'id' => $this->todo->id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Unable to create todo']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields']);
        }
    }

    // GET - Lire toutes les todos
    public function readAll()
    {
        $result = $this->todo->readAll();
        $num = $result->rowCount();

        if ($num > 0) {
            $todos_arr = [];

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $todo_item = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'completed' => (bool)$row['completed'],
                    'created_at' => $row['created_at']
                ];
                array_push($todos_arr, $todo_item);
            }

            http_response_code(200);
            echo json_encode($todos_arr);
        } else {
            http_response_code(200);
            echo json_encode([]);
        }
    }

    // GET - Lire une todo par ID
    public function read($id)
    {
        $this->todo->id = $id;

        if ($this->todo->readOne()) {
            $todo_arr = [
                'id' => $this->todo->id,
                'title' => $this->todo->title,
                'description' => $this->todo->description,
                'completed' => (bool)$this->todo->completed,
                'created_at' => $this->todo->created_at
            ];

            http_response_code(200);
            echo json_encode($todo_arr);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Todo not found']);
        }
    }

    // PUT - Mettre à jour une todo
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"));

        $this->todo->id = $id;

        if (!empty($data->title)) {
            $this->todo->title = $data->title;
            $this->todo->description = $data->description ?? '';
            $this->todo->completed = $data->completed ?? 0;

            if ($this->todo->update()) {
                http_response_code(200);
                echo json_encode(['message' => 'Todo updated']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Unable to update todo']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields']);
        }
    }

    // DELETE - Supprimer une todo
    public function delete($id)
    {
        $this->todo->id = $id;

        if ($this->todo->delete()) {
            http_response_code(200);
            echo json_encode(['message' => 'Todo deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Unable to delete todo']);
        }
    }
}
