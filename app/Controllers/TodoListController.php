<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TodoListModel;
use CodeIgniter\API\ResponseTrait;

class TodoListController extends BaseController
{
    use ResponseTrait;

    /**
     * TodoList Model.
     *
     * @var TodoListModel
     */
    protected TodoListModel $todoListModel;

    public function __construct()
    {
        $this->todoListModel = new TodoListModel();
    }

    /**
     * Render todo list view.
     *
     * @return void
     */
    public function view()
    {
        return view('todo');
    }
    /**
     * [Get] /todo
     * Get all todo list data.
     *
     * @return void
     */
    public function index()
    {
        // Find the data from database.
        $todoList = $this->todoListModel->findAll();

        return $this->respond([
            "msg"  => "success",
            "data" => $todoList
        ]);
    }

    /**
     * [GET] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function show(?int $key = null)
    {
        if ($key === null) {
            return $this->failNotFound("Enter the the todo key");
        }

        // Find the data from database.
        $todo = $this->todoListModel->find($key);

        if ($todo === null) {
            return $this->failNotFound("Todo is not found.");
        }

        return $this->respond([
            "msg" => "success",
            "data" => $todo
        ]);
    }

    /**
     * [POST] /todo
     * Create a new todo data into database.
     *
     * @return void
     */
    public function create()
    {
        // Get the  data from request.
        $data    = $this->request->getJSON();
        $title   = $data->title   ?? null;
        $content = $data->content ?? null;

        // Check if account and password is correct.
        if ($title === null || $content === null) {
            return $this->fail("Pass in data is not found.", 404);
        }

        if ($title === " " || $content === " ") {
            return $this->fail("Pass in data is not found.", 404);
        }

        // Insert data into database.
        $createdKey = $this->todoListModel->insert([
            "t_title"   => $title,
            "t_content" => $content,
            "created"   => date("Y-m-d H:i:s"),
            "updated"   => date("Y-m-d H:i:s"),
        ]);

        // Check if insert successfully.
        if ($createdKey === false) {
            return $this->fail("create failed.");
        } else {
            return $this->respond([
                "msg"  => "create successfully",
                "data" => $createdKey
            ]);
        }
    }

    /**
     * [PUT] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function update(?int $key = null)
    {
        // Get the  data from request.
        $data    = $this->request->getJSON();
        $title   = $data->title   ?? null;
        $content = $data->content ?? null;

        if ($key === null) {
            return $this->failNotFound("Key is not found.");
        }

        // Get the will update data.
        $willUpdateData = $this->todoListModel->find($key);

        if ($willUpdateData === null) {
            return $this->failNotFound("This data is not found.");
        }

        if ($title !== null) {
            $willUpdateData["t_title"] = $title;
        }

        if ($content !== null) {
            $willUpdateData["t_content"] = $content;
        }

        // Do update action.
        $isUpdated = $this->todoListModel->update($key, $willUpdateData);

        if ($isUpdated === false) {
            return $this->fail("Update failed.");
        } else {
            return $this->respond([
                "msg" => "Update successfully"
            ]);
        }
    }

    /**
     * [DELETE] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function delete(?int $key = null)
    {
        if ($key === null) {
            return $this->failNotFound("Key is not found.");
        }

        // Check the data is exist or not.
        if ($this->todoListModel->find($key) === null) {
            return $this->failNotFound("This data is not found.");
        }

        // Do delete action.
        $isDeleted = $this->todoListModel->delete($key);

        if ($isDeleted === false) {
            return $this->fail("Delete failed.");
        } else {
            return $this->respond([
                "msg" => "Delete successfully"
            ]);
        }
    }
}
