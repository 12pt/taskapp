<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private $db;
    
    function setUp() {
        # assume a db exists called taskapp where app@localhost has all permissions
        $this->db = new Database("localhost", "taskapp", "app", "foobar");
    }

    function testCanAddNewTask() {
        $title = "my task title";
        $content = "my task content";
        
        $result = $this->db->add($title, $content);

        $this->assertJsonStringEqualsJsonString(
            json_encode(array("title" => $title, "content" => $content)),
            $result
        );
    }

    function testCanGetAllTasks() {
        $result = $this->db->getAll();
    }

    function testCanUpdateATask() {
        $newTitle = "my updated title";
        $newContent = "my new content";
        $id = 1;

        $result = $this->db->update($id, $newTitle, $newContent);
        $result_decoded = json_decode($result, true);
        # expecting $result to have the old id but with new contents

        $this->assertEquals($result_decoded->{"id"}, 1);
        
        $this->assertJsonStringEqualsJsonString(
            json_encode(array("title" => $newTitle, "content" => $newContent)),
            $result
        );
    }

    function testCanDeleteATask() {
        $id = 1;
        $result = $this->db->delete($id);

        $this->assertEqual(
            $db->hasTask($id),
            false
        );
    }
}