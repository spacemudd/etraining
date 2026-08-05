# Adding Soketi & WebSockets Broadcast Guide

This guide details how to configure Soketi (WebSockets) in Laravel for real-time chat updates.

## 1. Environment Configuration (`.env`)
Update your `.env` file with your Soketi / Pusher credentials:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1

# Public Soketi endpoint (no shared Docker network — use Coolify HTTPS proxy)
PUSHER_HOST=ws.example.com
PUSHER_PORT=443
PUSHER_SCHEME=https

# Prefer runtime meta tags (app.blade.php) fed from Laravel `PUSHER_*` env —
# Echo reads those first so Coolify host/key changes do not require a Mix rebuild.
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
MIX_PUSHER_HOST="${PUSHER_HOST}"
MIX_PUSHER_PORT="${PUSHER_PORT}"
MIX_PUSHER_SCHEME="${PUSHER_SCHEME}"
```

## 2. Frontend Configuration (`resources/js/bootstrap.js`)
Echo prefers meta tags from Blade (`pusher-host`, etc.), then falls back to Mix env:

```javascript
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content
    || process.env.MIX_PUSHER_APP_KEY;
const pusherHost = document.querySelector('meta[name="pusher-host"]')?.content
    || process.env.MIX_PUSHER_HOST;
const pusherPort = Number(
    document.querySelector('meta[name="pusher-port"]')?.content
    || process.env.MIX_PUSHER_PORT
    || 443
);
const forceTLS = (document.querySelector('meta[name="pusher-scheme"]')?.content
    || process.env.MIX_PUSHER_SCHEME
    || 'https') === 'https';

if (pusherKey && pusherHost) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1',
        wsHost: pusherHost,
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: forceTLS,
        encrypted: forceTLS,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
}
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
