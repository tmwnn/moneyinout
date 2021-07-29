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
        summ: {
            total: 0,
            income: 0,
            outcome: 0,
        },
        stat: [],
        newItem: {
            date: typeof(curDate) !== "undefined" ? curDate : new Date().toISOString().substring(0,10),
            summ: 0,
            comment: '',
            category_id: 0,
            tags: '',
            type: 0,
        },
        editItem: {
            date: '',
            summ: 0,
            comment: '',
            category_id: 0,
            tags: '',
            type: 0,
        },
        catSettings: false,
        newCategory: '',
        filtersSettings: false,
        searchForm: {
            dateMin: '',
            dateMax: '',
            searchString: '',
            summMin: '',
            summMax: '',
            categories: [],
            type: '',
        },
        tableLoading: false,
        viewType: 'operations', // operations
        groupType: 'm',
        changePassword: false,
        connectTelegram: false,
        messengerTieWindow: false,
        messenger_field: '',
        messenger_qr_code: '',
        install_telegram_message: '',
        open_messenger_msg: '',
        messengerUrl: '',
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
                if (!!value) {
                    this.newItem.date = this.dateToStr(value);
                } else {
                    this.newItem.date = '';
                }
            }
        },
        xEditDate: {
            get: function () {
                return this.editItem.date;
            },
            set: function (value) {
                if (!!value) {
                    this.editItem.date = this.dateToStr(value);
                } else {
                    this.editItem.date = '';
                }
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
        xSearchDateMin: {
            get: function () {
                return this.searchForm.dateMin;
            },
            set: function (value) {
                if (!!value) {
                    this.searchForm.dateMin = this.dateToStr(value);
                } else {
                    this.searchForm.dateMin = '';
                }
            }
        },
        xSearchDateMax: {
            get: function () {
                return this.searchForm.dateMax;
            },
            set: function (value) {
                if (!!value) {
                    this.searchForm.dateMax = this.dateToStr(value);
                } else {
                    this.searchForm.dateMax = '';
                }
            }
        },
    },
    methods: {
        dateToStr: function (value) {
            let date = new Date(value);
            let strDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + (date.getDate())).slice(-2);
            return strDate;
        },
        load: function () {
            this.edit = 0;
            this.tableLoading = true;
            axios.post(loadUrl, {search: this.searchForm, page: this.page, type: this.viewType, group: this.groupType})
                .then((response) => {
                    this.tableLoading = false;
                    this.operations = response.data.operations;
                    this.summ = response.data.summ;
                    this.page = response.data.operations.current_page;
                    this.categories = response.data.categories;
                    if (this.viewType == 'graph') {
                        this.stat = response.data.stat;
                        this.loadGraph();
                    }
                    if (this.viewType == 'stat') {
                        this.stat = response.data.stat;
                    }

                    //console.log(response.data);
                })
                .catch((error) => {
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
                        this.newItem = {
                            date: typeof(curDate) !== "undefined" ? curDate : new Date().toISOString().substring(0,10),
                            summ: 0,
                            comment: '',
                            category_id: 0,
                            tags: '',
                            type: 0,
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
            let a = dateStr.toString().split('-');
            if (a.length == 3) {
                return a[2] + '.' + a[1] + '.' + a[0];
            }
            else if (a.length == 2) {
                return a[1] + '.' + a[0];
            }
            else {
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
                this.page = 1;
                this.load();
            }
        },
        searchTag: function(tag){
            this.searchForm.searchString = tag;
            this.page = 1;
            this.load();
        },
        clearForm: function() {
            this.searchForm = {
                dateMin: '',
                dateMax: '',
                searchString: '',
                summMin: '',
                summMax: '',
                categories: [],
                type: '',
            };
            this.load();
        },

        loadGraph: function () {
            let income = [];
            let outcome = [];
            let total = [];

            for (let i = 0; i < this.stat.length; i++) {
                if (this.stat[i].income) {
                    income.push([
                        this.stat[i].date,
                        this.stat[i].income,
                    ]);
                }
                if (this.stat[i].outcome) {
                    outcome.push([
                        this.stat[i].date,
                        this.stat[i].outcome,
                    ]);
                }
                if (this.stat[i].total) {
                    total.push([
                        this.stat[i].date,
                        this.stat[i].total,
                    ]);
                }
            }
            let height = '60%';
            if (document.querySelector('body').clientWidth <= 500) {
                height = '300';
            }
            Highcharts.stockChart('container', {
                chart: {
                    height: height,
                },
                rangeSelector: {
                    selected: 'all'
                },
                title: {
                    text: ''
                },
                yAxis: [
                    {
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: 'Доход'
                        },
                        height: '60%',
                        lineWidth: 2,
                        resize: {
                            enabled: true
                        }
                    },
                    {
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: 'Остаток'
                        },
                        top: '65%',
                        height: '35%',
                        offset: 0,
                        lineWidth: 2
                    },
                ],
                tooltip: {
                    split: false,
                    shared: true,
                },
                series: [
                    {
                        type: 'line',
                        name: 'Доход',
                        data: income,
                        color: '#3db378',
                    },
                    {
                        type: 'line',
                        name: 'Расход',
                        data: outcome,
                        color: '#ea4f4a',
                    },
                    {
                        type: 'column',
                        name: 'Остаток',
                        data: total,
                        yAxis: 1,
                        color: '#7cb5ec', // '#434348',
                    }
                ]
            });
        },
        changeGroupType: function (type) {
            this.groupType = type;
            this.load();
        },
        searchGroup: function (group) {
            let dateMin = group;
            let dateMax = group;
            if (group.toString().length == 4) {
                dateMin += '-01-01';
                dateMax += '-12-31';
            }
            if (group.toString().length == 7) {
                dateMin += '-01';

                if (/-(04|06|09|11)$/.test(group)) {
                    dateMax += '-30';
                } else if (group == '2020-02' || group == '2016-02' || group == '2012-02') {
                    dateMax += '-29';
                } else if (/-(02)$/.test(group)) {
                    dateMax += '-28';
                } else {
                    dateMax += '-31';
                }
            }

            this.searchForm = {
                dateMin: dateMin,
                dateMax: dateMax,
                searchString: '',
                summMin: '',
                summMax: '',
                categories: [],
            };
            this.page = 1;
            this.viewType = 'operations';
            this.filtersSettings = true;
            this.load();
        },
        messengerTie: function () {
            this.messenger_qr_code = 'Чтобы открыть Telegram на телефоне просканируйте код:';
            this.install_telegram_message = 'Установить Telegram на компьютер можно по <a href="https://tlgrm.ru/" target="_blank">ссылке</a>';
            this.open_messenger_msg = 'Чтобы открыть Telegram на компьютере перейдите по';
            this.messengerUrl = typeof(telegramUrl) !== 'undefined' ? telegramUrl.toString() : '';
            document.getElementById('qrcode').innerHTML = '';
            let qrcode = new QRCode('qrcode', {width: 230, height: 230});
            qrcode.clear();
            qrcode.makeCode(this.messengerUrl);
            this.connectTelegram = true;
        },
        messengerUnTie: function () {
            document.getElementById('telegram_chat_id').value = 0;
            document.getElementById('submitBtn').click();
        }

    },
    created: function () {
        if (typeof(loadUrl) !== 'undefined') {
            this.load();
        }
    },
});
