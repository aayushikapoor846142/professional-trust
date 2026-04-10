import 'bootstrap';
import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'tv1234',
//     cluster:'mt1',
//     wsHost:window.location.hostname,    
//     // wsHost:window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost'?window.location.hostname:'websockets.trustvisory.com',
//     wsPort: 6001,
//     wssPort: 6001,
//     forceTLS: window.location.hostname != '127.0.0.1' && window.location.hostname != 'localhost',
//     disableStats: true,
//     enabledTransports:['ws','wss'],
//     activityTimeout: 10000,
//     authEndpoint :$("#siteurl").attr("href")+"broadcasting/auth"
// });

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: PSKEY, // from your .env or config
    cluster: PSCLS, // from your .env or config
    forceTLS: false,
    encrypted: true,
});