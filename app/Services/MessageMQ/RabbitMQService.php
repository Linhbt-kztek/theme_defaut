<?php

namespace App\Services\MessageMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function publish($message, $name = 'test_exchange', $to = 'test_queue', $routing_key = 'test_key')
    {
        try {
            // dd(config('messageMQ.MQ_HOST'), config('messageMQ.MQ_PORT'), config('messageMQ.MQ_USER'), config('messageMQ.MQ_PASS'));
            $connection = new AMQPStreamConnection(config('messageMQ.MQ_HOST'), config('messageMQ.MQ_PORT'), config('messageMQ.MQ_USER'), config('messageMQ.MQ_PASS'));

            $channel = $connection->channel();
            $channel->exchange_declare($name, 'direct', false, false, false);
            $channel->queue_declare($to, false, false, false, false);
            $channel->queue_bind($to, $name, $routing_key);
            $msg = new AMQPMessage($message);
            $channel->basic_publish($msg, $name, $routing_key);
            $channel->close();
            $connection->close();

            return [
                'status' => 200,
                'message' => $message
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 90,
                'message' => $th->getMessage()
            ];
        }

    }

    // public function consume()
    // {
    //     // $connection_info = [env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST')];
    //     $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'));

    //     dd($connection);
    //     $channel = $connection->channel();
    //     $callback = function ($msg) {
    //         echo ' [x] Received ', $msg->body, "\n";
    //     };
    //     $channel->queue_declare('test_queue', false, false, false, false);
    //     $channel->basic_consume('test_queue', '', false, true, false, false, $callback);
    //     echo 'Waiting for new message on test_queue', " \n";
    //     dd($channel);
    //     while ($channel->is_consuming()) {
    //         $channel->wait();
    //     }
    //     $channel->close();
    //     $connection->close();
    // }

    // public function consume()
    // {
    //     $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'));
    //     $channel = $connection->channel();
    //     $callback = function ($msg) {
    //         echo ' [x] Received ', $msg->body, "\n";
    //     };
    //     $channel->queue_declare('test_queue', false, false, false, false);
    //     $channel->basic_consume('test_queue', '', false, true, false, false, $callback);
    //     echo 'Waiting for new message on test_queue', " \n";

    //     // Vòng lặp vô hạn để tiếp tục tiêu thụ tin nhắn
    //     // while (count($channel->callbacks)) {
    //     //     $channel->wait();
    //     // }

    //     $channel->close();
    //     $connection->close();
    // }

}