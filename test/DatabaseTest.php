<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private $db;
    
    function setUp() {
        # assume a db exists called taskapp_test where app@localhost has all permissions
        $this->db = new Database("localhost", "taskapp_test", "app", "foobar");
    }

    private function _selectRandomListing() {
        $listings = json_decode($this->db->getAll(), true);
        return $listings[array_rand($listings)];
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
        $randomChoice = $this->_selectRandomListing();
        $id = (string) $randomChoice["id"];

        # save a copy of its old data so we can compare it to the new data
        $oldTitle = $randomChoice["title"];
        $oldContent = $randomChoice["content"];

        $newTitle = "my updated title";
        $newContent = "my new content";

        $result = $this->db->update($id, $newTitle, $newContent);
        $this->assertNotNull($result);

        $result_decoded = json_decode($result, true);
        # expecting $result to have the old id but with new contents

        $this->assertEquals($result_decoded["id"], $id);
        
        # check the updated task contains what we wanted
        $this->assertJsonStringEqualsJsonString(
            json_encode(array("title"   => $newTitle,
                              "content" => $newContent)),
            json_encode(array("title"   => $result_decoded["title"],
                              "content" => $result_decoded["content"]))
        );
    }

    function testCanDeleteATask() {
        $id = (string) $this->_selectRandomListing()["id"];
        #echo "deleting row $id";

        $result = $this->db->delete($id); # will return the id of the deleted row.
        $this->assertNotNull($result);
        $this->assertEquals(json_decode($result, true)["id"], $id);

        $deletionCheck = $this->db->hasTask($id);
        print_r($deletionCheck);

        $this->assertEquals(
            json_decode($deletionCheck, true)["count"],
            0
        );
    }
}