<?php

namespace App\Test\Queue\Factories;

use App\Test\BaseTestCase;
use BrighteCapital\QueueClient\Queue\Factories\QueueClientFactory;
use BrighteCapital\QueueClient\Queue\Sqs\SqsClient;
use Enqueue\Sqs\SqsDestination;

class QueueClientFactoryTest extends BaseTestCase
{
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new QueueClientFactory();
    }

    public function testCreate()
    {
        $client = $this->factory->create([
            'provider' => 'sqs',
            'queue' => 'queue',
            'region' => 'ap-southeast-2',
        ]);
        $destination = $client->getDestination();
        $this->assertInstanceOf(SqsClient::class, $client);
        $this->assertInstanceOf(SqsDestination::class, $destination);
        $this->assertEquals('ap-southeast-2', $destination->getRegion());
    }

    public function testCreateFailed()
    {
        try {
            $this->assertInstanceOf(
                SqsClient::class,
                $this->factory->create(['provider' => 'test', 'queue' => 'queue'])
            );
        } catch (\Exception $e) {
            $this->assertStringContainsString('Failed to create Queue Client test', $e->getMessage());
        }
    }

    public function testCreateQueueMissing()
    {
        try {
            $this->assertInstanceOf(SqsClient::class, $this->factory->create(['provider' => 'test']));
        } catch (\Exception $e) {
            $this->assertEquals('Please provide Queue name', $e->getMessage());
        }
    }
}
