/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */
const debug = process.env.NODE_ENV !== 'production'

import Vue from 'vue'
import Vuex from 'vuex'
import logrocketPlugin from './plugins/logrocket'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {},
    strict: debug,
    plugins: [logrocketPlugin],
})
