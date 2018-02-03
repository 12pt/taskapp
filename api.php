<?php declare(strict_types=1);

define("API_RESOURCE", "api.php");

require_once(__DIR__ . "/database.php");

global $db;
$db = new Database("localhost", "taskapp_test", "app", "foobar");

/**
 * Get all tasks.
 */
function get($label) {
    global $db;
    return $db->getAll();
}

/**
 * Add a new task. Test with:
 * curl -v -X POST localhost:8000/api.php/ -d "title=my awesome title&content=my new task content goes here"
 */
function post() {
    global $db;
    if(isset($_POST["title"]) && isset($_POST["content"])) {
        $title = $_POST["title"];
        $content = $_POST["content"];
        return $db->add($title, $content);
    } else {
        header("HTTP/1.1 400 Bad Request");
    }
}

/**
 * Modify a task. Test with:
 * curl -v -X PUT localhost:8000/api.php/111 -d "title=my updated title&content=my updated new task content goes here"
 */
function put($label) {
    if(isset($label) && strlen(trim($label)) > 0) {
        global $db;
        parse_str(file_get_contents("php://input"), $put);
        
        $id = $label;
        $title = $put["title"];
        $content = $put["content"];
        return $db->update($id, $title, $content);
    }
}

/**
 * Delete a task.
 */
function delete(string $label) { # label is the id e.g. ../api.php/23
    global $db;
    return $db->delete($label);
}

/**
 * Given a supported HTTP verb, delegate a method to handle it, return the response from that method
 * to be passed back up.
 */
function respond(string $method, string $label) {
    switch($method) {
    case "GET": return get($label);
    case "POST": return post($label);
    case "PUT": return put($label);
    case "DELETE": return delete($label);
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        header("Allow: GET, PUT, DELETE");
    }
}

/**
 * Routing function, ensure we're in the right place, handle most 404s.
 */
function route(string $path, string $method) {
    $paths = explode("/", $path);
    array_shift($paths); # get rid of first element
    $resource = array_shift($paths);

    if ($resource == constant("API_RESOURCE")) {
        return respond($method, array_shift($paths));
    } else {
        header("HTTP/1.1 404 Not Found");
    }
}

if(array_key_exists("REQUEST_URI", $_SERVER)) {
    echo route($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);
}