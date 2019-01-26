require('./bootstrap');

import Vue from 'vue';
import store from './store';
import VueCookie from 'vue-cookie';

Vue.use(VueCookie);

global.Vue = Vue;

import App from './components/App';


const app = new Vue({
    el: '#kp-generator-app',
    store,
    components: { App }
});
