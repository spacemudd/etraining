/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

import LogRocket from 'logrocket';
import createPlugin from 'logrocket-vuex';

let logRocketEnabled = document.head.querySelector('meta[name="logrocket-enabled"]');

if (window.location.hostname.includes('.com') && logRocketEnabled) {
    LogRocket.init(process.env.MIX_LOGROCKET_KEY);
    let logRocketId = document.head.querySelector('meta[name="logrocket-id"]');
    let logRocketIdExtra = document.head.querySelector('meta[name="logrocket-id-extra"]');
    if (logRocketId) {
        LogRocket.identify(logRocketId.content, JSON.parse(logRocketIdExtra.content));
    }
}

export default new createPlugin(LogRocket)
