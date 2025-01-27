<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskControllerTest extends WebTestCase
{
    public function testCreateTask()
    {
        $client = static::createClient();
        $client->request('POST', '/task', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending'
        ]));

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $jsonContent = json_decode($response->getContent(), true);
        // Проверяем, что ключ 'title' существует в ответе
        $this->assertArrayHasKey('title', $jsonContent);
        $this->assertEquals('New Task', $jsonContent['title']);
    }

    public function testGetTasks()
    {
        $client = static::createClient();
        $client->request('POST', '/task', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending'
        ]));
        $client->request('GET', '/task');

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $jsonContent = json_decode($response->getContent(), true);

        // Проверяем, что ответ является массивом
        $this->assertIsArray($jsonContent);
    }

}
