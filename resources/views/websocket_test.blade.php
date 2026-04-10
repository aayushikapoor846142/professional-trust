<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

<h1>Websocket Test</h1>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>

<!-- Include Echo via CDN -->
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.10.0/dist/echo.iife.js"></script>


<script>

   window.Pusher = Pusher;
        //console.log(window.location.hostname);
        window.Echo = new Echo({
            broadcaster: 'pusher',
            wsHost: window.location.hostname,
            key: 'tv1234',  // Your Pusher key
            cluster: 'mt1',  // Your Pusher cluster
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,

        });
        window.Echo.connector.pusher.connection.bind('state_change', function(state) {
            console.log(state); // This will log the connection state to help debug issues
        });


   //console.log( window.Echo.channel('chatting'));
        window.Echo.channel('chatting')
        .listen('.MessageSent', (event) => {
            alert('Message sentnew:hi kapoor');
             console.log('Message sentnew:hi kapoor');
        });
        const channel = window.Echo.channel('chatting');

        // Listen for events on that channel
        channel.listen('.MessageSent', (event) => {
            console.log('Message received: ', event);
        });

        // Debugging subscription
        channel.subscribed(() => {
            alert('sasamsamsnm');
            console.log('Successfully subscribed to chat channel');
        });

        channel.error((error) => {
            console.error('Error subscribing to channel:', error);
        });

    // Listen to the 'chat-channel' and the 'MessageSent' event
    // console.log( window.Echo.channel('chatting'));
    //     window.Echo.channel('chatting')
    //     .listen('.blueyellow', (event) => {
    //         alert('Message sentnew:hi aayushi');
    //          console.log('Message sentnew:hi aayushi');
    //     });


</script>
</body>
</html>