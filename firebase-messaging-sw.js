// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here, other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js');
// importScripts('https://www.gstatic.com/firebasejs/7.1.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/7.1.0/firebase-analytics.js');
// importScripts('https://www.gstatic.com/firebasejs/7.1.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp({
    apiKey: "AIzaSyBc5a0MW_MJ7Jg4TfMNdAUC2j7EqcoN-WY",
    authDomain: "mclean-910c7.firebaseapp.com",
    databaseURL: "https://mclean-910c7.firebaseio.com",
    projectId: "mclean-910c7",
    storageBucket: "mclean-910c7.appspot.com",
    messagingSenderId: "670679497477",
    appId: "1:670679497477:web:9a441f96a1cc98aa3e156c",
    measurementId: "G-M186NDWXCC"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();