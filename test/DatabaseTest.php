<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    function testCanCreateDatabase() {
        # assume a db exists called taskapp where app@localhost has all permissions
        $db = new Database("localhost", "taskapp", "app", "foobar");
    }

    function testCanAddNewTask() {
    }

    function testCanGetAllTasks() {
    }

    function testCanUpdateATask() {
    }

    function testCanDeleteATask() {
    }
}