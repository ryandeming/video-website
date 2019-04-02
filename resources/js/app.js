
import Vue from 'vue'
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

 const files = require.context('./', true, /\.vue$/i);
 files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require('turbolinks').start();

Turbolinks.scroll = {};

$(document).on('click', '[data-turbolinks-scroll=false]', function(e) {
  Turbolinks.scroll['top'] = $(document).scrollTop();
});

document.addEventListener('turbolinks:load', (event) => {
  const root = document.getElementById('app')

  if (window.vue) {
      window.vue.$destroy(true)
  }

  window.vue = new Vue({
      render: h => h(
          Vue.component(root.dataset.component), {
              props: JSON.parse(root.dataset.props)
          }
      )
  }).$mount(root)

  if (Turbolinks.scroll['top']) {
    $(document).scrollTop(Turbolinks.scroll['top']);
    Turbolinks.scroll = {};
  }
});