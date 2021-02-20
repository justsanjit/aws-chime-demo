<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Aws\Chime\ChimeClient;
use Aws\Credentials\Credentials;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function show(Meeting $meeting)
    {
        return view('meeting.show', [
            'meeting' => $meeting,
            'attendeeResponse' => session('meeting-'.$meeting->id)
        ]);
    }

    public function create()
    {
        return view('meeting.create');
    }

    public function index()
    {
        return view('meeting.index', [
            'meetings' => Meeting::all()
        ]);
    }
    
    public function store()
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
    
        $response = $chime->createMeeting([
            'ClientRequestToken' => Str::random(24),
            'MediaRegion' => $region
        ]);

        Meeting::create([
            'meeting_id' => $response['Meeting']['MeetingId'],
            'response' => $response->toArray()
        ]);
            
        return redirect('meetings');
    }
}
