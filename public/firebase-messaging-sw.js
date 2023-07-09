importScripts('https://www.gstatic.com/firebasejs/5.8.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.8.2/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the
firebase.initializeApp({
    messagingSenderId: "878691844233"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('Received background message [firebase-messaging-sw.js]: ', payload);

    const notification = JSON.parse(payload.data.notification);

    // Customize notification here
    const notificationTitle = notification.title;
    const notificationOptions = {
        icon: notification.icon,
        body: notification.body
    };

    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});
