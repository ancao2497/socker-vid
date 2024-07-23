<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewMessage;
use App\Events\UserJoinedGroup;
use Pusher\Pusher;
use stdClass;
use App\Http\Controllers\JsonResponse;

use function PHPUnit\Framework\isEmpty;

class YourController extends Controller
{
    public function index(Request $request)
    {   
        $groupId = '1'; // Example group ID
        broadcast(new NewMessage($groupId))->toOthers();
        return response()->json($groupId);
    }
    public function loginReverb(Request $request)
    {
        // channel name will be something like private-room-231 where 231 is accountId
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        $channelPieces = explode('-', $channelName);
        $channelAccount = end($channelPieces);

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
            if (empty($channels->channels)) {
                return response()->json([]);
            }
            // broadcast(new NewMessage($channels))->toOthers();
            return response()->json($channels);
        } catch (\Throwable $th) {
            return response()->json([]);
        }
       
    }
}