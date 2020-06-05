/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Vue from 'vue';
import axios from "axios/index";
import Notify from 'vue2-notify';
import VueConfirmDialog from "vue-confirm-dialog";
//import {ServerTable, ClientTable, Event} from 'vue-tables-2';

Vue.use(Notify, {position: 'top-right'});
Vue.use(VueConfirmDialog);

window.Vue = require('vue');

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
//Vue.use(ClientTable);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const App = new Vue({
    el: '#app',
    data: {
        add: 0,
        edit: 0,
        page: 1,
        searchString: '',
        operations: {
            data: [],
        },
        categories: [],
        summ: 0,
        columns: [
            'id',
            'date',
            'summ',
            'category_id',
            'comment'
        ],
        newItem: {
            date: '',
            summ: 0,
            comment: '',
            category_id: 0,
        },
        editItem: {
            date: '',
            summ: 0,
            comment: '',
            category_id: 0,
        }
    },
    computed: {
        categoriesAssoc: function() {
            let data = {};
            this.categories.forEach((item) => {
                data[item.id] = item.name;
            });
            return data;
        },
    },
    methods: {
        load: function () {
            axios.post(loadUrl, {search: this.searchString, page: this.page,})
                .then((response) => {
                    this.operations = response.data.operations;
                    this.summ = response.data.summ;
                    this.page = response.data.operations.current_page;
                    this.categories = response.data.categories;
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error.response);
                    Vue.$notify(error.response.data.message, 'error');
                })
            ;
        },
        save: function (url, id, fields) {
            let data = {};
            fields.forEach((field) => {
                data.id = id;
                data[field] = this.$refs[field + '_' + id].value;
            });
            axios.post(url, data)
                .then((response) => {
                    if (this.checkResult(response.data)) {
                        document.location.reload();
                    }
                })
                .catch(function (error) {
                    console.log(error.response);
                    Vue.$notify(error.response.data.message, 'error');
                })
            ;
        },
        saveRow: function (url, id) {
            axios.post(url, this.editItem)
                .then((response) => {
                    if (this.checkResult(response.data)) {
                        this.load();
                    }
                })
                .catch(function (error) {
                    console.log(error.response);
                    Vue.$notify(error.response.data.message, 'error');
                })
            ;
        },
        storeRow: function (url) {
            axios.post(url, this.newItem)
                .then((response) => {
                    if (this.checkResult(response.data)) {
                        this.load();
                    }
                })
                .catch(function (error) {
                    console.log(error.response);
                    Vue.$notify(error.response.data.message, 'error');
                })
            ;
        },
        remove: function (url, id) {
            this.$vueConfirm.confirm(
                {
                    auth: false,
                    message: 'Вы уверены?',
                    button: {
                        no: 'Нет',
                        yes: 'Да'
                    }
                },
                (confirm) => {
                    if (confirm == true) {
                        axios.post(url, {id: id})
                            .then((response) => {
                                if (this.checkResult(response.data)) {
                                    document.location.reload();
                                }
                            })
                            .catch(function (error) {
                                console.log(error.response);
                                Vue.$notify(error.response.data.message, 'error');
                            })
                        ;
                    }
                }
            );
        },
        editRow: function (id) {
            this.operations.data.forEach((row) => {
                console.log(id, row);
                if (row.id == id) {
                    this.editItem = row;
                }
            });
            this.edit = id;
        },
        checkResult: function (data) {
            if (!!data.errors) {
                Vue.$notify(!!data.message ? data.message : 'Неизвестная ошибка!', 'error');
                return false;
            }
            return true;
        },
        setPage: function (page) {
            this.page = page;
            this.load();
        }
    },
    created: function () {
        if (typeof(loadUrl) !== 'undefined') {
            this.load();
        }
    },
});
