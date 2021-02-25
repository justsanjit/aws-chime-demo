<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meeting {{ $meeting->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-5">
                        <div>
                            <video id="local-video"></video>
                            Local Video
                        </div>
                        <div>
                            <video id="video-1"></video>
                            Tile 1
                        </div>
                        <div>
                            <video id="video-2"></video>
                            Tile 2
                        </div>
                        <div>
                            <video id="video-3"></video>
                            Tile 3
                        </div>
                        <div>
                            <video id="video-4"></video>
                            Tile 4
                        </div>
                        <div>
                            <video id="video-5"></video>
                            Tile 5
                        </div>
                        <div>
                            <video id="video-6"></video>
                            Tile 6
                        </div>
                        <div>
                            <video id="video-7"></video>
                            Tile 7
                        </div>
                        <div>
                            <video id="video-8"></video>
                            Tile 8
                        </div>
                        <div>
                            <video id="video-9"></video>
                            Tile 9
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

                        const videoElements = [
                            domElement('video-1'),
                            domElement('video-2'),
                            domElement('video-3'),
                            domElement('video-4'),
                            domElement('video-5'),
                            domElement('video-6'),
                            domElement('video-7'),
                            domElement('video-8'),
                            domElement('video-9'),
                        ];

                        // index-tileId pairs
                        const indexMap = {};

                        const acquireVideoElement = tileId => {
                        // Return the same video element if already bound.
                        for (let i = 0; i < 9; i += 1) {
                            if (indexMap[i] === tileId) {
                                return videoElements[i];
                            }
                        }
                        // Return the next available video element.
                        for (let i = 0; i < 9; i += 1) {
                            if (!indexMap.hasOwnProperty(i)) {
                            indexMap[i] = tileId;
                            return videoElements[i];
                            }
                        }
                        throw new Error('no video element is available');
                        };

                        const releaseVideoElement = tileId => {
                        for (let i = 0; i < 9; i += 1) {
                            if (indexMap[i] === tileId) {
                                delete indexMap[i];
                                return;
                                }
                            }
                        };

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

                                if (tileState.localTile) {
                                    meetingSession.audioVideo.bindVideoElement(tileState.tileId, domElement('local-video'));
                                    reutrn;
                                }

                                // Ignore a tile without attendee ID, a local tile (your video), and a content share.
                                if (!tileState.boundAttendeeId || tileState.isContent) {
                                    return;
                                }

                                meetingSession.audioVideo.bindVideoElement(
                                    tileState.tileId,
                                    acquireVideoElement(tileState.tileId)
                                );
                            },
                            videoTileWasRemoved: tileId => {
                                releaseVideoElement(tileId);
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
