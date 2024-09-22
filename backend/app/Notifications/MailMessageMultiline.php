<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class MailMessageMultiline extends MailMessage
{
    protected function formatLine($line) {
        if (is_array($line)) {
            return implode(' ', array_map('trim', $line));
        }
        // Just return without removing new lines.
        return trim($line);
    }
}
