<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
<script>
    const firebaseConfigGlobal = {
        apiKey: "{{ env('FIREBASE_API_KEY') }}",
        authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
        databaseURL: "{{ env('FIREBASE_DATABASE_URL') }}",
        projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
        storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
        appId: "{{ env('FIREBASE_APP_ID') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENT_ID') }}"
    };
    firebase.initializeApp(firebaseConfigGlobal);

    // Push messaging butuh secure context (https/localhost) dan Service Worker
    const messaging = firebase.messaging.isSupported() ? firebase.messaging() : null;

    function requestNotificationPermission() {
        if (!messaging) {
            return;
        }
        messaging.requestPermission()
            .then(function() {
                console.log('Notification permission granted.');
                return messaging.getToken();
            })
            .then(function(token) {
                registerToken(token);
            })
            .catch(function(err) {
                if (Notification.permission === 'denied') {
                    alert('Notification permission denied. You can allow notifications from your browser settings.');
                } else if (Notification.permission === 'default') {
                    console.log('User dismissed the notification permission request.');
                }
            });
    }

    function registerToken(token) {
        $.ajax({
            url: '{{ route("register.token") }}',
            type: 'POST',
            data: {
                token: token,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('Error registering token:', error);
            }
        });
    }

    // Call the function to request notification permission
    requestNotificationPermission();

    // Handle incoming messages
    if (messaging) {
        messaging.onMessage(function(payload) {
            console.log('Message received. ', payload);
            // Customize notification here
            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon
            };

            new Notification(notificationTitle, notificationOptions);
        });
    }
</script>

