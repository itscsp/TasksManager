<?php
namespace TasksManager\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use TasksManager\Tasks_Taxonomy;

class TasksTaxonomyTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_register_taxonomy() {
        // Mock WordPress functions
        $mock = Monkey\Functions\expect('register_taxonomy')
            ->once()
            ->with('project', 'task', \Mockery::type('array'));

        $taxonomy = new Tasks_Taxonomy();
        $taxonomy->register_taxonomy();
        
        $this->assertTrue(true); // Assert that we got here without errors
    }

    public function test_taxonomy_args() {
        $taxonomy = new Tasks_Taxonomy();
        $reflection = new \ReflectionClass($taxonomy);
        $method = $reflection->getMethod('get_taxonomy_args');
        $method->setAccessible(true);

        $args = $method->invoke($taxonomy);

        $this->assertIsArray($args);
        $this->assertTrue($args['hierarchical']);
        $this->assertTrue($args['show_ui']);
        $this->assertTrue($args['show_admin_column']);
        $this->assertEquals('project', $args['rewrite']['slug']);
    }
}
