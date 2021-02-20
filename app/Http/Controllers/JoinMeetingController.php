<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Aws\Chime\ChimeClient;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JoinMeetingController extends Controller
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
    
        $result = $chime->createAttendee([
            'ExternalUserId' => 'user-'.Auth::id(),
            'MeetingId' => $meeting->meeting_id
        ]);

        session()->put('meeting-'.$meeting->id, $result->toArray());

        return redirect('meetings/'.$meeting->id);
    }
}
