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
import DatePicker from 'vue2-datepicker-yi-bootstrap';
//import {ServerTable, ClientTable, Event} from 'vue-tables-2';
import vSelect from 'vue-select'

import 'vue-select/dist/vue-select.css';

Vue.use(Notify, {position: 'top-right'});
Vue.use(VueConfirmDialog);

//Vue.component('v-select', vSelect)
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
    components: {
        'date-picker': DatePicker,
        'v-select': vSelect,
    },
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
            date: typeof(curDate) !== "undefined" ? curDate : new Date().toISOString().substring(0,10),
            summ: 0,
            comment: '',
            category_id: 0,
        },
        editItem: {
            date: '',
            summ: 0,
            comment: '',
            category_id: 0,
        },
        catSettings: false,
        newCategory: '',
        filtersSettings: false,
        searchForm: {
            searchString: '',
            summMin: '',
            summMax: '',
        },
        tableLoading: false,
    },
    computed: {
        categoriesAssoc: function() {
            let data = {};
            this.categories.forEach((item) => {
                data[item.id] = item.name;
            });
            return data;
        },
        categoriesSelect: function() {
            let data = [];
            this.categories.forEach((item) => {
                data.push({label: item.name, code: item.id, user_id: item.user_id});
            });
            return data;
        },
        xNewDate: {
            get: function () {
                return this.newItem.date;
            },
            set: function (value) {
                let dateStr = new Date(value).toISOString().substring(0,10);
                this.newItem.date = dateStr;
            }
        },
        xEditDate: {
            get: function () {
                return this.editItem.date;
            },
            set: function (value) {
                let dateStr = new Date(value).toISOString().substring(0,10);
                this.editItem.date = dateStr;
            }
        },
        xNewCategory: {
            get: function () {
                return this.categoriesAssoc[this.newItem.category_id];
            },
            set: function (value) {
                this.newItem.category_id = value.code;
            },
        },
        xEditCategory: {
            get: function () {
                return this.categoriesAssoc[this.editItem.category_id];
            },
            set: function (value) {
                this.editItem.category_id = value.code;
            },
        },
    },
    methods: {
        load: function () {
            this.edit = 0;
            this.tableLoading = true;
            axios.post(loadUrl, {search: this.searchForm, page: this.page})
                .then((response) => {
                    this.tableLoading = false;
                    this.operations = response.data.operations;
                    this.summ = response.data.summ;
                    this.page = response.data.operations.current_page;
                    this.categories = response.data.categories;
                    //console.log(response.data);
                })
                .catch(function (error) {
                    this.tableLoading = false;
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
                        this.newItem = {
                            date: curDate,
                            summ: 0,
                            comment: '',
                            category_id: 0,
                        };
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
        removeRow: function (url, id) {
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
                                    this.load();
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
                //console.log(id, row);
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
        },
        dateConvert: function (dateStr) {
            let a = dateStr.split('-');
            if (a.length == 3) {
                return a[2] + '.' + a[1] + '.' + a[0];
            } else {
                return dateStr;
            }

        },
        catStore: function () {
            console.log('catStore');

            axios.post('/dashboard/store_category', {name: this.newCategory})
                .then((response) => {
                    if (this.checkResult(response.data)) {
                        this.newCategory = '';
                        this.load();
                    }
                })
                .catch(function (error) {
                    console.log(error.response);
                    Vue.$notify(error.response.data.message, 'error');
                })
            ;

        },
        catSave: function (id, value) {
            console.log('catSave', id);
            this.categories.forEach((item) => {
                if (item.id == id) {
                    axios.post('/dashboard/update_category', {id: id, name: item.name})
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
                }
            });
        },
        catDel: function (id) {
            console.log('catDel', id);
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
                        axios.post('/dashboard/delete_category', {id: id})
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
                    }
                }
            );
        },
        searchStringKeyup: function (e) {
            if (e.keyCode === 13) {
                this.load();
            }
        },
    },
    created: function () {
        if (typeof(loadUrl) !== 'undefined') {
            this.load();
        }
    },
});
