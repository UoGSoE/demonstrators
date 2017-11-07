
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * the below promise stuff is stolen from Adam Wathan, see :
 * https://gist.github.com/adamwathan/babd10ed0e971404c5d8a86358d01b61
 */

// Creates a new promise that automatically resolves after some timeout:
Promise.delay = function (time) {
    return new Promise((resolve, reject) => {
        setTimeout(resolve, time)
    })
}

// Throttle this promise to resolve no faster than the specified time:
Promise.prototype.takeAtLeast = function (time) {
    return new Promise((resolve, reject) => {
        Promise.all([this, Promise.delay(time)]).then(([result]) => {
            resolve(result)
        }, reject)
    })
}


window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Datepicker from 'vuejs-datepicker';
import ToggleButton from 'vue-js-toggle-button'

Vue.component('datepicker', Datepicker);
Vue.component('demonstrator-request', require('./components/DemonstratorRequest.vue'));
Vue.component('staff-request', require('./components/StaffDemonstratorRequest.vue'));
Vue.component('student-application', require('./components/StudentApplication.vue'));


Vue.use(ToggleButton)

const app = new Vue({
    el: '#app'
});
