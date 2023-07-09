@extends('layouts.app')

@section('title', ':: Test Push Notification')

@push('scripts-bottom')
    <script src="https://www.gstatic.com/firebasejs/5.8.2/firebase.js"></script>

    <script>
        // Initialize Firebase
        const config = {
            apiKey: "AIzaSyDM8ZemZxu38CQOUM7igdIIZT-Sq2nXZp4",
            authDomain: "travelbuddi-appetiser.firebaseapp.com",
            databaseURL: "https://travelbuddi-appetiser.firebaseio.com",
            projectId: "travelbuddi-appetiser",
            storageBucket: "travelbuddi-appetiser.appspot.com",
            messagingSenderId: "878691844233",
            appId: "1:878691844233:web:80d719225276cef0d54658",
            measurementId: "G-VKNKERCVC8"
        };

        firebase.initializeApp(config);

        let device_token;

        const messaging = firebase.messaging();

        messaging.requestPermission().then(() => {
            console.log('FCM push notification permission granted.');

            $('#firebase-cloud-messaging-info').html('FCM push notification permission granted.');

            return messaging.getToken(); // Get the token in the form of promise
        }).catch(error => {
            console.log('Error', error.message);

            $('#messaging').html(error.message);
        });

        messaging.onMessage(payload => {
            console.log('Payload', payload);

            $('#firebase-cloud-messaging-info').html(JSON.stringify(payload));
        });

        $(document).ready(() => {
            $('#obtain-device-token').click(e => {
                e.preventDefault();

                messaging.getToken().then(token => {
                    console.log('Device token', token);

                    $('#device-token').val(token);
                }).catch(error => {
                    console.log('Error', error.message);

                    $('#messaging').html(error.message);
                });
            });

            $('#send-notification').click(e => {
                e.preventDefault();

                axios.post('/ajax/notifications/send', {
                    device_token: $('#device-token').val()
                }).then(response => {
                    console.log('Success', response);
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="container py-5">
        <div class="alert alert-info" role="alert">
            <div id="firebase-cloud-messaging-info">Firebase Cloud Messaging (FCM) push notification.</div>
        </div>

        <div class="card">
            <div class="card-header font-weight-bold">Test Push Notification</div>

            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="device-token" class="font-weight-bold">Device Token</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button id="obtain-device-token" class="btn btn-primary" type="button">
                                    <i class="fa fa-cube" aria-hidden="true"></i> Obtain
                                </button>
                            </div>

                            <input type="text" id="device-token" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button id="send-notification" class="btn btn-primary">
                            <i class="fa fa-bell" aria-hidden="true"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
