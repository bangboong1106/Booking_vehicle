importScripts('https://www.gstatic.com/firebasejs/3.9.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/3.9.0/firebase-messaging.js');


// Initialize Firebase
var config = {
    messagingSenderId: "670679497477"
};

firebase.initializeApp(config);
const messaging = firebase.messaging();