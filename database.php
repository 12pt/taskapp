<?php declare(strict_types=1);

final class Database {
    public function __construct(string $hostname, string $dbname, string $user, string $pass) {
        try {
            $dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->pdo = new PDO($dsn, $user, $pass, $opt);

            $this->_checkDatabaseHasTables();
        } catch(PDOException $e) {
            print("An error occured when initializing the database: " . $e->getMessage());
        }
    }

    private function _checkDatabaseHasTables() {
        # this can be pure SQL
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id           INTEGER       NOT NULL AUTO_INCREMENT,
            title        VARCHAR(64)   NOT NULL DEFAULT 'No Title',
            content      VARCHAR(255)  NOT NULL,
            completed    BOOLEAN       NOT NULL DEFAULT FALSE,
            date_created TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id));";
        $this->pdo->exec($sql);
    }

    private function _errorJson(string $message) {
        return json_encode(array("error" => $message));
    }

    private function _get(string $id) {
        try {
            $stmnt = $this->pdo->prepare("SELECT * FROM tasks WHERE id=:id");
            $stmnt->bindParam(":id", $id);
            $stmnt->execute();
            $result = $stmnt->fetch(PDO::FETCH_ASSOC);
            return json_encode($result);

        } catch(PDOException $e) {
            return $this->_errorJson("unable to find row with id $id");
        }
    }

    public function add(string $title, string $content) {
        try {
            $stmnt = $this->pdo->prepare("INSERT INTO tasks (title,content) VALUES (:title, :content)");
            $stmnt->bindParam(":title", $title);
            $stmnt->bindParam(":content", $content);
            $stmnt->execute();
            return $this->_get($this->pdo->lastInsertId());

        } catch(PDOException $e) {
            return $this->_errorJson("unable to add row with keys $title and $content");
        }
    }

    public function getAll() {
        try {
            $stmnt = $this->pdo->prepare("SELECT * FROM tasks");
            $stmnt->execute();
            return json_encode($stmnt->fetchAll(PDO::FETCH_ASSOC));
        } catch(PDOException $e) {
            return $this->_errorJson("unable to get all tasks.");
        }
    }

    public function update(string $id, string $title, string $content) {
        try {
            $stmnt = $this->pdo->prepare("UPDATE tasks SET title=:title, content=:content WHERE id=:id");
            $stmnt->bindParam(":title", $title);
            $stmnt->bindParam(":content", $content);
            $stmnt->bindParam(":id", $id);
            $stmnt->execute();
            return $this->_get($id);
        } catch(PDOException $e) {
            return $this->_errorJson("unable to update $id with values \"$title\" & \"$content\"");
        }
    }

    public function delete(string $id) {
        try {
            $stmnt = $this->pdo->prepare("DELETE FROM tasks WHERE id=:id");
            $stmnt->bindParam(":id", $id);
            $stmnt->execute();
            return json_encode(array("id" => $id));
        } catch(PDOException $e) {
            return $this->_errorJson("unable to delete $id.");
        }
    }

    public function hasTask(string $id) {
        try {
            $stmnt = $this->pdo->prepare("SELECT COUNT(*) FROM tasks WHERE id=:id");
            $stmnt->bindParam(":id", $id);
            $stmnt->execute();
            $result = $stmnt->fetch(PDO::FETCH_ASSOC);
            $count = $result["COUNT(*)"];

            return json_encode(array(
                "id" => $id,
                "count" => $count));

        } catch(PDOException $e) {
            return $this->_errorJson("unable to check if a task exists with id $id.");
        }
    }
}