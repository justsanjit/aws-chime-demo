<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Aws\Chime\ChimeClient;
use Aws\Chime\Exception\ChimeException;
use Aws\Credentials\Credentials;

class BroadcastController extends Controller
{
    public function __invoke(Meeting $meeting)
    {
        $credentials = new Credentials(
            config('services.chime.key'),
            config('services.chime.secret')
        );
    
        $region = config('services.chime.region');
    
        $chime = new ChimeClient([
            'credentials' => $credentials,
            'region' => $region,
            'version' => 'latest',
        ]);

        $result = rescue(
            fn () => $chime->createAttendee([
                'ExternalUserId' => 'transcoding-service',
                'MeetingId' => $meeting->meeting_id
            ])->toArray(),
            fn () => null,
            false
        );

        return view('broadcast', [
            'attendee' => $result,
            'meeting' => $meeting
        ]);
    }
}
