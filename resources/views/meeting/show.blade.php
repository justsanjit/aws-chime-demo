<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meeting {{ $meeting->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-2">
                        <div>
                            <video id="local-video"></video>
                            Local Video
                        </div>
                        <div>
                            <video id="remote-video"></video>
                            Remote video
                        </div>
                    </div>
                    <audio id="audio"></audio>


                    <script src="/js/app.js"></script>

                    <script>
                        window.app = window.app || {};
                        window.app.attendeeResponse = @json($attendeeResponse);
                        window.app.meetingResponse = @json($meeting->response);

                        const logger = new chime.ConsoleLogger('MyLogger', chime.LogLevel.INFO);
                        const deviceController = new chime.DefaultDeviceController(logger);

                        // You need responses from server-side Chime API. See below for details.
                        const meetingResponse = window.app.meetingResponse;
                        const attendeeResponse = window.app.attendeeResponse;
                        const configuration = new chime.MeetingSessionConfiguration(meetingResponse, attendeeResponse);

                        // In the usage examples below, you will use this meetingSession object.
                        const meetingSession = new chime.DefaultMeetingSession(
                            configuration
                            , logger
                            , deviceController
                        );

                        const videoElement = document.getElementById('video-element');

                        function domElement(id) {
                            return document.getElementById(id);
                        }

                        const observer = {
                            audioVideoDidStart: () => {
                                console.log('Started');
                                meetingSession.audioVideo.startLocalVideoTile();
                            }
                            , audioVideoDidStop: sessionStatus => {
                                // See the "Stopping a session" section for details.
                                console.log('Stopped with a session status code: ', sessionStatus.statusCode());
                            }
                            , audioVideoDidStartConnecting: reconnecting => {
                                if (reconnecting) {
                                    // e.g. the WiFi connection is dropped.
                                    console.log('Attempting to reconnect');
                                }
                            }
                            , videoTileDidUpdate: tileState => {
                                const videoElement = tileState.localTile ? 'local-video' : 'remote-video'

                                meetingSession.audioVideo.bindVideoElement(tileState.tileId, domElement(videoElement));
                            }
                        };

                        meetingSession.audioVideo.addObserver(observer);



                        (async () => {

                            // Make sure you have choosen audio device. In this case, you will choose first device.
                            const firstAudioDeviceId = (await meetingSession.audioVideo.listAudioInputDevices())[0].deviceId;
                            await meetingSession.audioVideo.chooseAudioInputDevice(firstAudioDeviceId);

                            // Make sure you have chosen your camera. In this use case, you will choose the first device.
                            const videoInputDevices = await meetingSession.audioVideo.listVideoInputDevices();
                            await meetingSession.audioVideo.chooseVideoInputDevice(videoInputDevices[0].deviceId);

                            // Bind audio to video session
                            meetingSession.audioVideo.bindAudioElement(domElement('audio'));

                            meetingSession.audioVideo.start();
                        })();

                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
