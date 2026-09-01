<?php

declare(strict_types=1);

return [
    /*
    | When true, card checkout is blocked and trainees see an outage notice.
    | Visitors are stored in payment_outage_interest for later WhatsApp follow-up.
    */
    'gateway_unavailable' => (bool) env('PAYMENT_GATEWAY_UNAVAILABLE', false),
];
