<?php

namespace App\Interfaces\ChatServices\Drivers;

use App\Interfaces\ChatServices\ChatServiceInterface;
use App\Models\Users\FirebaseUsers;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Factory;
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;

class FirebaseChatService implements ChatServiceInterface
{
	protected 	$serviceAccount;
	protected  	$client;
	protected $error;

	private $icon ;

	public function __construct()
	{
		$this->icon = env( 'CHAT_ICON' );
		$this->serviceAccount  = ServiceAccount::fromJsonFile( base_path().'/'.env( 'FIREBASE_JSON_FILE' ) );
		$this->setClient();
	}

	private function setClient()
	{
		$this->client = new Client();
		$this->client->setApiKey( env('FCM_SERVER_KEY') );
		$this->client->injectHttpClient(new \GuzzleHttp\Client());

	}

	public function sendMessageToUser( $recipient_id, $sender_id, $message , $data = [] , $badge = 1 )
	{
		$sound = isset( $data['sound'] ) ? $data['sound'] : 'default';

		// used for android oreo

		$data['gcm.notification.android_channel_id'] = isset( $data[ 'pn_type' ] ) ? $data[ 'pn_type' ] : '';
		
		// check if user has a registered device

		if( ! $firebase_user = FirebaseUsers::byUserId( $recipient_id ) ){
			$this->error  = ' User\'s device is not currently registered ';
			return false;
		}

		$device_token = $firebase_user->device_token;
		$title = isset( $data['title'] ) ? $data['title'] : '';

		$note = new Notification(  $title, $message );
		$note->setIcon( $this->icon );
		$note->setColor( '#2ea8ff' )
			->setSound( $sound )
			->setBadge( $badge );

		$message = new Message();
		$message->addRecipient(new Device( $device_token ));
		$message->setNotification( $note )
			->setData( $data );

		$response = $this->client->send( $message );

		return [
			'response' => $response
		];

	}

	public function sendMessageToChannel( $channel_id, $device_tokens = [] , $message , $data = [])
	{
		$note = new Notification( '', $message );
		$note->setIcon( $this->icon );
		$note->setColor( '#2ea8ff' )
			->setBadge(1);

		$message = new Message();
		if( is_array( $device_tokens ) ){
			foreach( $device_tokens as $device_token ){
				$message->addRecipient(new Device( $device_token ));
			}
		}else{
			$message->addRecipient( new Device( $device_tokens ) );
		}

		$message->setNotification($note)
			->setData(array( 'channel_id' => $channel_id ));

		$response = $this->client->send( $message );

		return [
			'response' => $response
		];

	}

	public function sendMessageToDevices( $device_tokens = [] , $message , $data = [])
	{
		$sound = isset( $data['sound'] ) ? $data['sound'] : 'default';
		$note = new Notification( '', $message );
		//$note->setIcon('notification_icon_resource_name')
		$note->setColor( '#ffffff' )
			->setBadge(1)
			->setSound( $sound );

		$message = new Message();
		if( is_array( $device_tokens ) ){
			foreach( $device_tokens as $device_token ){
				$message->addRecipient(new Device( $device_token ));
			}
		}else{
			$message->addRecipient( new Device( $device_tokens ) );
		}

		$message->setNotification($note)
			->setData( $data );

		$response = $this->client->send( $message );

		return [
			'response' => $response
		];

	}

	public function getError()
	{
		return $this->error;
	}
}