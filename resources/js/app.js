import './bootstrap'

import Vue from 'vue'
import App from './App.vue'

import router from './plugins/router'
import vuetify from './plugins/vuetify'

import axios from 'axios'
import VueAxios from 'vue-axios'

import moment from 'vue-moment'
import VueShortkey from 'vue-shortkey'
import VueSweetalert2 from 'vue-sweetalert2'

import 'sweetalert2/dist/sweetalert2.min.css'

Vue.use(moment)
Vue.use(VueShortkey)
Vue.use(VueSweetalert2)
Vue.use(VueAxios, axios)

const app = new Vue({
  el: '#app',
  router,
  vuetify,
  components: { App }
});
