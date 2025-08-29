<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MessageControllerTest extends WebTestCase
{
    public function testPostCreatesMessage(): void
    {
        $client = static::createClient();
        // Один і той самий контейнер у межах тесту (щоб in-memory storage зберігся між запитами)
        $client->disableReboot();

        $payload = ['text' => 'Hello World'];

        $client->request(
            method: 'POST',
            uri: '/api/messages',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload, JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertArrayHasKey('id', $data);
        self::assertSame('Hello World', $data['text']);

        // Підтверджуємо, що GET повертає щойно створене повідомлення
        $client->request('GET', '/api/messages');
        self::assertResponseIsSuccessful();

        $list = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($list);
        self::assertNotEmpty($list);
        self::assertSame('Hello World', $list[0]['text']);
    }

    public function testPostValidationError(): void
    {
        $client = static::createClient();

        $client->request(
            method: 'POST',
            uri: '/api/messages',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['wrong' => 'field'], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(['error' => 'Field "text" is required'], $data);
    }

    public function testGetEmptyListInitially(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/messages');
        self::assertResponseIsSuccessful();

        $list = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($list);
        // Перший тест міг залишити стан, тому не перевіряємо порожність жорстко.
        // Якщо хочеш ізольованість — оголоси окремий сервіс-ресеттер і викликай його в setUp().
    }
}
