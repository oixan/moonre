
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import VueRouter from 'vue-router'
import Vuetify from 'vuetify';
import VeeValidate from 'vee-validate';
import VueAwesomeSwiper from 'vue-awesome-swiper'
import Masonry from '../../../node_modules/masonry-layout/dist/masonry.pkgd';
import VueI18n from 'vue-i18n'
import Language from './common/language.ts';

require('./bootstrap');
const routes = require('./app-router').default;

window.Vue = require('vue');
window.Masonry = Masonry;

const i18n = (new Language()).vueI18n;


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    routes
})

// Route Guard
    router.beforeEach((to, from, next) => {

        if( to.meta.requiresAuth ) {
            var token = window.localStorage.getItem('token');
            if ( token )
                next();
            else{
                window.localStorage.removeItem('token');
                window.localStorage.removeItem('user');
                next('/');
            }

        }

        next();
        
    })
// End - Route Guard

Vue.use(VueI18n)
Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VeeValidate);
Vue.use(VueAwesomeSwiper, /* { default global options } */)

Vue.component('list-moods', require('./components/moods/list-moods.vue'));
Vue.component('app-mood', require('./components/moods/mood.vue'));
Vue.component('addMoods-modal', require('./components/moods/addMood-modal.vue'));
Vue.component('app-logo', require('./components/logo.vue'));
Vue.component('mood-Detail', require('./components/moods/mood-detail.vue'));

Vue.component('view-login', require('./login/login.vue'));
Vue.component('view-logon', require('./logon/logon.vue'));

Vue.component('app-navigationLeft', require('./layout/navigationLeft.vue'));
Vue.component('app-navigationLeftHome', require('./components/navigations/left/home.vue'));
Vue.component('app-container', require('./layout/container.vue'));
Vue.component('app-header', require('./layout/header.vue'));

Vue.component('app-settingUser', require('./setting/user.vue'));
Vue.component('app-settingPasswordReset', require('./setting/passwordReset.vue'));
Vue.component('app-settingDeleteAccount', require('./setting/deleteAccount.vue'));

Vue.component('layout', require('./layout/layout.vue'));

window.AppEvent = new Vue();

const app = new Vue({
    el: '#moonre',
    router,
    i18n
});
