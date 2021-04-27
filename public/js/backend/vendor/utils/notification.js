let config = {
    apiKey: "AIzaSyBc5a0MW_MJ7Jg4TfMNdAUC2j7EqcoN-WY",
    authDomain: "mclean-910c7.firebaseapp.com",
    databaseURL: "https://mclean-910c7.firebaseio.com",
    projectId: "mclean-910c7",
    storageBucket: "mclean-910c7.appspot.com",
    messagingSenderId: "670679497477",
    appId: "1:670679497477:web:9a441f96a1cc98aa3e156c",
    measurementId: "G-M186NDWXCC"
};
firebase.initializeApp(config);
const messaging = firebase.messaging();

messaging.requestPermission()
    .then(function () {
        // console.log('***token', tokenM);

        return messaging.getToken();
    })
    .then(function (token) {
        // console.log('***token', token);
        updateFcmToken(token);
    })
    .catch(function (err) { // Happen if user deney permission
        console.log('*****Unable to get permission to notify.', err);
    });

messaging.onMessage(function (payload) {
    showNotification(payload);
});

function updateFcmToken(token) {
    $.ajax({
        url: updateTokenFcm,
        data: {
            token: token
        },
        dataType: 'json',
        success: function () {
        }
    });
}

function showNotification(payload) {
    if (payload.data.webAdmin === 'true') {
        $.ajax({
            url: displayNotification,
            dataType: 'html',
            data: {
                payload: payload
            },
            success: function (response) {
                var notiItem = $('.notification-box-item');
                notiItem.html('');
                notiItem.append(response);
                notiItem.fadeIn();
                setTimeout(function () {
                    notiItem.fadeOut();
                }, 10000);
            }
        });

        $.ajax({
            url: urlUpdateNotification,
            dataType: 'html',
            data: {
                payload: payload
            },
            success: function (response) {
                var notification = $('.notification-list-header');
                if (notification) {
                    notification.html('');
                    notification.append(response);
                }
            }
        });
    }
}

function makeReadAll() {
    $.ajax({
        url: makeReadAllNotification,
        dataType: 'html',
        data: {
            req: '1'
        },
        success: function (response) {
            var notification = $('.notification-list-header');
            if (notification) {
                notification.html('');
                notification.append(response);
            }
        }
    });
}