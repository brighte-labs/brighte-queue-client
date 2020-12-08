<?php

namespace BrighteCapital\QueueClient\Queue\Factories;

use BrighteCapital\QueueClient\Queue\QueueClientInterface;
use BrighteCapital\QueueClient\Queue\Sqs\SqsClient;
use BrighteCapital\QueueClient\Queue\Sqs\SqsConnectionFactory;
use Enqueue\Sqs\SqsDestination;

class QueueClientFactory
{
    public const PROVIDERS_SQS = 'sqs';
    public const PROVIDERS_KAFKA = 'kafka';
    public const PROVIDERS_RABBIT_MQ = 'rabbit_mq';

    /**
     * @param array $config config
     * @return QueueClientInterface
     * @throws \Exception
     */
    public function create(array $config): QueueClientInterface
    {
        $provider = $config['provider'] ?? 'undefined';

        if (!isset($config['queue'])) {
            throw new \Exception('Please provide Queue name');
        }

        switch ($provider) {
            case self::PROVIDERS_SQS:
                $sqsConnectFactory = new SqsConnectionFactory($config);
                $context = $sqsConnectFactory->createContext();
                $queue = $context->createQueue($config['queue']);
                if ($queue instanceof SqsDestination && isset($config['region'])) {
                    $queue->setRegion($config['region']);
                }

                return new SqsClient($queue, $context);
        }

        throw new \Exception(sprintf('Failed to create Queue Client %s', $provider));
    }
}
