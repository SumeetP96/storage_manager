import './bootstrap'
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import router from './plugins/router'
import vuetify from './plugins/vuetify'
import App from './App.vue'
import moment from 'vue-moment'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(moment)
Vue.use(VueAxios, axios)
Vue.use(VueSweetalert2)

const app = new Vue({
  el: '#app',
  router,
  vuetify,
  components: { App }
});
