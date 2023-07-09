<?php

namespace App\Interfaces\ChatServices;

interface ChatServiceInterface {

    public function sendMessageToUser( $recipient_id, $sender_id, $message , $data = [] );
    public function sendMessageToChannel( $channel_id, $devices , $message , $data = [] );
    public function sendMessageToDevices( $devices , $message , $data = [] );
    public function getError();


}