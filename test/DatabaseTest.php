<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private $db;
    
    function setUp() {
        # assume a db exists called taskapp_test where app@localhost has all permissions
        $this->db = new Database("localhost", "taskapp_test", "app", "foobar");
    }

    function testCanAddNewTask() {
        $title = "my task title";
        $content = "my task content";
        
        $result = json_decode($this->db->add($title, $content), true);

        $this->assertNotNull($result);
        $this->assertEquals(
            $title,
            $result["title"]
        );
        $this->assertEquals(
            $content,
            $result["content"]
        );
    }

    function testCanGetAllTasks() {
        $result = $this->db->getAll();
        $this->assertNotNull($result);
    }

    function testCanUpdateATask() {
        # randomly select an existing task to update
        $listings = json_decode($this->db->getAll(), true);
        $randomChoice = array_rand($listings);

        $id = $randomChoice["id"];

        $oldTitle = $randomChoice["title"]
        $oldContent = $randomChoice["content"]

        $newTitle = "my updated title";
        $newContent = "my new content";

        $result = $this->db->update($id, $newTitle, $newContent);
        $this->assertNotNull($result);

        $result_decoded = json_decode($result, true);
        # expecting $result to have the old id but with new contents

        $this->assertEquals($result_decoded->{"id"}, $id);
        
        $this->assertJsonStringEqualsJsonString(
            json_encode(array("title" => $newTitle, "content" => $newContent)),
            json_encode(array("title" => $oldTitle, "content" => $oldContent))
        );
    }

    function testCanDeleteATask() {
        $id = 1;
        $result = $this->db->delete($id);

        $this->assertNotNull($result);
        $this->assertEqual(
            $db->hasTask($id),
            false
        );
    }
}