<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private $db;
    
    /**
     * Ensure we can instantiate the Database class and that a database connection
     * was successfully established.
     */
    function setUp() {
        # assume a db exists called taskapp_test where app@localhost has all permissions
        $dbinfo = include(__DIR__ . '../config.php');
        $this->db = new Database($dbinfo["host"], $dbinfo["dbname"], $dbinfo["username"], $dbinfo["password"]);
    }

    /**
     * Gets all listings from the database and selects one at random.
     *
     * @return JSON object containing a random task.
     */
    private function _selectRandomListing() {
        $listings = json_decode($this->db->getAll(), true);
        return $listings[array_rand($listings)];
    }

    /**
     * Check whether we can add a new task to the database.
     */
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

    /**
     * Check if we can get all tasks in the database.
     */
    function testCanGetAllTasks() {
        $result = $this->db->getAll();
        $this->assertNotNull($result);
    }

    /**
     * Check we can update a task. Selects a random task, and then requests an update of it.
     * Checks that the updated data is the same as the test update data.
     */
    function testCanUpdateATask() {
        $randomChoice = $this->_selectRandomListing();
        $id = (string) $randomChoice["id"];

        # TODO: make this randomly generated in case we update a previously updated task.
        $newTitle = "my updated title [" . (string) rand() . "]";
        $newContent = "my new content [" . (string) rand() . "]";

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

    /**
     * Check we can delete a task. Checks we have deleted it by looking for a task by its id again.
     */
    function testCanDeleteATask() {
        $id = (string) $this->_selectRandomListing()["id"];
        #echo "deleting row $id";

        $result = $this->db->delete($id); # will return the id of the deleted row.
        $this->assertNotNull($result);
        $this->assertEquals(json_decode($result, true)["id"], $id);

        $deletionCheck = $this->db->hasTask($id);

        $this->assertEquals(
            json_decode($deletionCheck, true)["count"],
            0
        );
    }
}