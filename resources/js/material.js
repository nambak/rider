require('./bootstrap');

window.Vue = require('vue').default;

import Vue from "vue";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import VueQrcodeReader from 'vue-qrcode-reader';
import vuetify from './plugins/vuetify' // path to vuetify export
import 'vuetify/dist/vuetify.min.css'
import colors from 'vuetify/lib/util/colors'

Vue.use(VueSweetalert2);
Vue.use(VueQrcodeReader);

// ./components/ExampleComponent.vue -> <example-component></example-component>
Vue.component('qr-code-scanner', require('./components/QRCodeScanner.vue').default);

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const app = new Vue({
    vuetify,
    el: '#app',
});
