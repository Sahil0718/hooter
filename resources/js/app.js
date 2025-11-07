import './bootstrap';

// Initialize Pusher Beams Client
const beamsClient = new PusherPushNotifications.Client({
    instanceId: '4f60ec0a-f1a0-42a4-8d5b-b35898050c6f', // Your Instance ID
});

// Start the Beams Client
beamsClient.start()
    .then(() => beamsClient.addDeviceInterest('all-users'))
    .then(() => {
        console.log('Successfully registered for notifications!');
        // Request notification permission if not granted
        if (Notification.permission !== 'granted') {
            return Notification.requestPermission();
        }
    })
    .then(() => {
        if (Notification.permission === 'granted') {
            console.log('Notifications enabled!');
        }
    })
    .catch(error => {
        console.error('Error setting up notifications:', error);
    });