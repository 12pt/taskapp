<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    function testCanCreateDatabase() {
        $db = new Database();
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