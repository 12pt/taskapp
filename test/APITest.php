<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../api.php';

use PHPUnit\Framework\TestCase;

class APITest extends TestCase {
    function setUp() {
        # api will not use a class.
    }
    
    public function testGET() {
        route("/api.php/", "GET");
    }

    public function testPUT() {
        route("/api.php/", "PUT");
    }

    public function testDELETE() {
        route("/api.php/", "DELETE");
    }
}