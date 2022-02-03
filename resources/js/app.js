/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from "vue";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import VueQrcodeReader from 'vue-qrcode-reader';

Vue.use(VueSweetalert2);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueQrcodeReader);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('qr-code-scanner', require('./components/QRCodeScanner.vue').default);
Vue.component('order-pickup', require('./components/order/PickUp.vue').default);
Vue.component('my-orders', require('./components/order/MyOrders.vue').default);
Vue.component('delivery-complete', require('./components/order/DeliveryComplete.vue').default);
Vue.component('admin-users', require('./components/admin/user/List.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 *
 */

Vue.mixin({
    methods: {
        showError(error) {
            let errorText = error.message;

            if (typeof error.response !== 'undefined') {
                if (typeof error.response.data === 'string') {
                    errorText = error.response.data;
                } else if (error.response.data.message) {
                    errorText = error.response.data.message;
                }
            }

            this.$swal({
                icon: 'error',
                title: '오류',
                text: errorText,
            });
        },
    }
})

const app = new Vue({
    el: '#app',
});
