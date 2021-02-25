<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Aws\Chime\ChimeClient;
use Aws\Chime\Exception\ChimeException;
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
    
        try {
            $result = $chime->createAttendee([
                'ExternalUserId' => 'user-'.Auth::id(),
                'MeetingId' => $meeting->meeting_id
            ]);
        } catch (ChimeException $e) {
            if ($e->getAwsErrorCode() === 'NotFoundException') {
                return back()->with('message', 'Meeting not found. Try to create new meeting.');
            }
        }

        session()->put('meeting-'.$meeting->id, $result->toArray());

        return redirect('meetings/'.$meeting->id);
    }
}
