# Adding Soketi & WebSockets Broadcast Guide

This guide details how to configure Soketi (WebSockets) in Laravel for real-time chat updates.

## 1. Environment Configuration (`.env`)
Update your `.env` file with your Soketi / Pusher credentials:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_HOST=your-soketi-server-ip-or-domain
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

## 2. Frontend Configuration (`resources/js/bootstrap.js`)
Ensure Laravel Echo is properly configured with Pusher to connect to your Soketi server:

```javascript
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    wsHost: process.env.MIX_PUSHER_HOST || window.location.hostname,
    wsPort: process.env.MIX_PUSHER_PORT || 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});
```

## 3. Creating the Event (`WhatsAppMessageReceived`)
Run the following command to create an event:
```bash
php artisan make:event WhatsAppMessageReceived
```

Update `app/Events/WhatsAppMessageReceived.php`:
```php
<?php

namespace App\Events;

use App\Models\Back\WhatsAppMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public WhatsAppMessage $message)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('whatsapp-chat');
    }
}
```

## 4. Triggering the Event
Dispatch the event whenever a new inbound or outbound message/note is stored:
```php
broadcast(new WhatsAppMessageReceived($message));
```

## 5. Listening on the Frontend (`Index.vue`)
In your Vue component (`resources/js/Pages/Back/Chat/Index.vue`), listen to the channel in `mounted()`:

```javascript
mounted() {
    this.loadConversations();
    if (this.configured) {
        this.loadTemplates();
    }

    if (window.Echo) {
        window.Echo.channel('whatsapp-chat')
            .listen('WhatsAppMessageReceived', (e) => {
                this.loadConversations();
                if (this.selectedConversation && e.message.phone === this.selectedConversation.phone) {
                    this.loadMessagesSilently();
                }
            });
    }
},
```
