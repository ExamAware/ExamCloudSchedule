<?php
set_time_limit(0);

require 'path/to/Ratchet/Http/HttpServer.php';
require 'path/to/Ratchet/Server/IoServer.php';
require 'path/to/Ratchet/WebSocket/WsServer.php';
require 'path/to/Ratchet/MessageComponentInterface.php';
require 'path/to/Ratchet/ConnectionInterface.php';

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ConfigChangeNotifier implements MessageComponentInterface {
    protected $clients;
    protected $subscriptions;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if ($data['action'] === 'subscribe') {
            $configId = $data['configId'];
            $this->subscriptions[$configId][] = $from;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->subscriptions as $configId => $subscribers) {
            $this->subscriptions[$configId] = array_filter($subscribers, function($subscriber) use ($conn) {
                return $subscriber !== $conn;
            });
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    public function notifyClients($configId) {
        if (isset($this->subscriptions[$configId])) {
            foreach ($this->subscriptions[$configId] as $client) {
                $client->send(json_encode(['action' => 'reload', 'configId' => $configId]));
            }
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ConfigChangeNotifier()
        )
    ),
    8080
);

$server->run();
?>
