<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../api.php';
require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class APITest extends TestCase {
    private $db;

    function setUp() {
        $this->db = new Database("localhost", "taskapp_test", "app", "foobar");
    }
    
    function testGET() {
        $result = route("/api.php/", "GET");
        $this->assertJsonStringEqualsJsonString($result, $this->db->getAll());
    }

    function testPOST() {
        # sketchy edit of POST but its for testing purposes and shouldn't affect production
        $_POST["title"] = "my new title [" . rand() . "]";
        $_POST["content"] = "my new content [" . rand() . "]";
        $result = json_decode(route("/api.php/", "POST"), true);

        $this->assertEquals($_POST["title"],
                    $result["title"]);
        $this->assertEquals($_POST["content"],
                    $result["content"]);
    }

    function testPUT() {
        # can't really test PUTting here
    }

    function testDELETE() {
        # ensure a row exists, rather than selecting a random existing one
        $_POST["title"] = "my new title [" . rand() . "]";
        $_POST["content"] = "my new content [" . rand() . "]";
        $result = json_decode(route("/api.php/", "POST"), true);

        $id = $result["id"];
        route("/api.php/$id", "DELETE");

        $deletionCheck = $this->db->hasTask((string) $id);
        $this->assertEquals(
            json_decode($deletionCheck, true)["count"],
            0);
    }
}