
<div id="currentToken"></div>

<script src="https://www.gstatic.com/firebasejs/5.9.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.9.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.9.0/firebase-messaging.js"></script>

<script src="https://www.gstatic.com/firebasejs/5.9.0/firebase.js"></script>
<script>
    // Initialize Firebase
    var config = {
        apiKey: "AIzaSyCYqbD9i8BmmUoWCfrspWhwumIeJ-SmXJw",
        authDomain: "web-base-plate.firebaseapp.com",
        databaseURL: "https://web-base-plate.firebaseio.com",
        projectId: "web-base-plate",
        storageBucket: "web-base-plate.appspot.com",
        messagingSenderId: "179422952942"
    };
    firebase.initializeApp(config);

    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(function() {
            console.log('Notification permission granted.');
            messaging.getToken()
                .then(function(currentToken) {
                    if (currentToken) {
                        console.log(currentToken);
                        document.getElementById("currentToken").innerHTML=''+currentToken+'';
                    } else {
                        console.log('No Instance ID token available. Request permission to generate one.');
                    }
                })
                .catch(function(err) {
                    console.log('An error occurred while retrieving token. ', err);
                });
        })
        .catch(function(err) {
            console.log('Unable to get permission to notify. ', err);
        });

    messaging.onMessage(function(payload) {
        console.log( "Message received. ", payload);
        alert( payload.notification.body );
    });
</script>


