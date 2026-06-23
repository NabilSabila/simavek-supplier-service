<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitmqService
{
    public static function publish(string $queue, array $payload): void
    {
        try {
            $host = env('RABBITMQ_HOST', 'rabbitmq');
            $port = (int) env('RABBITMQ_PORT', 5672);

            $connection = new AMQPStreamConnection($host, $port, 'guest', 'guest');
            $channel = $connection->channel();

            $channel->queue_declare($queue, false, true, false, false);

            $message = new AMQPMessage(
                json_encode($payload),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );

            $channel->basic_publish($message, '', $queue);
            $channel->close();
            $connection->close();

            Log::info("Event terkirim ke queue {$queue}", $payload);
        } catch (\Exception $e) {
            Log::error("Gagal kirim ke RabbitMQ: " . $e->getMessage());
        }
    }
}