<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct(){
        $this->clients = new \SplObjectStorage();
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn){
        $this->clients->attach($conn);
        echo "New Connection: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg){
        $data = json_decode($msg, true);

        //for user login
        if (isset($data['type']) && $data['type'] === 'login'){
            $this->users[$from->resourceId] = $data['user_id'];
            echo "User {$data['user_id']} connected\n";
            
            // shows if online
            $this->broadcast(json_encode([
                'type' => 'presence',
                'user_id' => $data['user_id'],
                'status' => 'online'
            ]));
            return;
        }

        // Broadcast to all other clients
        foreach ($this->clients as $client){
            if ($from !== $client){
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Handle presence: offline
        if (isset($this->users[$conn->resourceId])) {
            $userId = $this->users[$conn->resourceId];
            echo "User {$userId} disconnected.\n";
            $this->broadcast(json_encode([
                'type' => 'presence',
                'user_id' => $userId,
                'status' => 'offline'
            ]));
            unset($this->users[$conn->resourceId]);
        }
        
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function broadcast($msg) {
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }
}
