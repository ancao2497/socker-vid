<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewMessage;
use App\Events\UserJoinedGroup;
use Pusher\Pusher;
use stdClass;
use App\Http\Controllers\JsonResponse;

use function PHPUnit\Framework\isEmpty;

class ChannelController extends Controller
{
    public function index(Request $request)
    {   
        broadcast(new NewMessage(1))->toOthers();
        return response()->json($request);
    }
    public function loginReverb(Request $request)
    {
        // channel name will be something like private-room-231 where 231 is accountId
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        $channelPieces = explode('-', $channelName);
        $channelAccount = end($channelPieces);
        $connection = config('broadcasting.connections.reverb');
        $pusher = new Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            $connection['options'] ?? []
        );
        $auth = $pusher->authenticate(socketId, channel);
        // this verifies account id against a server known value
        // if ($channelAccount != $request->attributes->get('auth_account_id')) {
        //     return response()->json(['auth' => 'INVALID'], 403);
        // }

        // this generates the required format for the response
        $stringToAuth = $socketId . ':' . $channelName;
        $hashed = hash_hmac('sha256', $stringToAuth, env('REVERB_APP_SECRET'));

        return response()->json(['auth' => env('REVERB_APP_KEY') . ':' . $hashed]);
    }
    public function store(Request $request)
    {      
        try {
            $connection = config('broadcasting.connections.reverb');
            $pusher = new Pusher(
                $connection['key'],
                $connection['secret'],
                $connection['app_id'],
                $connection['options'] ?? []
            );
     
            $channels = $pusher->getChannels([]);
            // $channelList = $channels->channels;
            // var_dump($channels->channels);die;
            if (empty($channels->channels)) {
                return response()->json([]);
            }
            $result = [];
            // Iterate over each channel
            foreach ($channels->channels as $key => $value) {
                // Split the key into number and username
                $parts = explode('-', $key);
      
                // If the key can be split into exactly two parts (number and username)
                if (count($parts) === 2 && is_numeric($parts[0])) {
                    $number = $parts[0];
                    $username = $parts[1];
                    // Store the number as key and username as value in the result array
                    $result[] = [
                        'vid' => $number,
                        'user' => $username
                    ];
                }
            }
            return response()->json($channels);       
        } catch (\Throwable $th) {
            return response()->json([]);    
        }
            
    }
    public function create(Request $request)
    {   
        $vidToCheck = $request->input('vid');
        $vidToCheck = (int)$vidToCheck; 
        $exists = true;
        try {
            $connection = config('broadcasting.connections.reverb');
            $pusher = new Pusher(
                $connection['key'],
                $connection['secret'],
                $connection['app_id'],
                $connection['options'] ?? []
            );
     
            $channels = $pusher->getChannels([]);
         
            if (empty($channels->channels)) {
                return response()->json(true);
            }

            // Iterate over each channel
            foreach ($channels->channels as $key => $value) {
                // Split the key into number and username
                $parts = explode('-', $key);

                // If the key can be split into exactly two parts (number and username)
                if (count($parts) === 2 && is_numeric($parts[0])) {
                    $vid = (int)$parts[0];
                    if ($vid === $vidToCheck) {
                        $exists = false; 
                        break; // Exit the loop as we found the vid
                    }
                }
            }
           
            if ($exists) {
                $channels = $pusher->getChannels([]);
                broadcast(new NewMessage($channels))->toOthers();
                return response()->json(true);
            }else{
                $channels = $pusher->getChannels([]);
                broadcast(new NewMessage($channels))->toOthers();
                return response()->json(false);
            }
        } catch (\Throwable $th) {
            return response()->json(true);
        }
    }
    public function sendSignal(Request $request)
    {   
        try {
            $connection = config('broadcasting.connections.reverb');
            $pusher = new Pusher(
                $connection['key'],
                $connection['secret'],
                $connection['app_id'],
                $connection['options'] ?? []
            );
     
            $channels = $pusher->getChannels([]);
         
            if (empty($channels->channels)) {
                return response()->json(true);
            } 
            $channels = $pusher->getChannels([]);
            broadcast(new NewMessage($channels))->toOthers();
            return response()->json(true);
        } catch (\Throwable $th) {
            return response()->json(false);
        }
    }
}