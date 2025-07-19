<?php
namespace TasksManager\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use TasksManager\Tasks_Post_Type;

class TasksPostTypeTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_register_post_type() {
        // Mock WordPress functions
        $mock = Monkey\Functions\expect('register_post_type')
            ->once()
            ->with('task', \Mockery::type('array'));

        $post_type = new Tasks_Post_Type();
        $post_type->register_post_type();
        
        $this->assertTrue(true); // Assert that we got here without errors
    }

    public function test_post_type_args() {
        $post_type = new Tasks_Post_Type();
        $reflection = new \ReflectionClass($post_type);
        $method = $reflection->getMethod('get_post_type_args');
        $method->setAccessible(true);

        $args = $method->invoke($post_type);

        $this->assertIsArray($args);
        $this->assertTrue($args['public']);
        $this->assertEquals('tasks', $args['rewrite']['slug']);
        $this->assertContains('title', $args['supports']);
        $this->assertContains('editor', $args['supports']);
        $this->assertContains('comments', $args['supports']);
    }
}
