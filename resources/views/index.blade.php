@extends('layouts.base')

@section('title', __('menu.main'))

@section('content')
    <div class="container">

        <div class="tab-pane mb-2">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Что ищем" aria-label="Что ищем" aria-describedby="basic-addon2"
                       v-model="searchForm.searchString" v-on:keyup="searchStringKeyup" >
                <div class="input-group-append">
                    <button class="input-group-text fa  fa-search text-success" @click="page=1;load();" title="Найти">&nbsp;</button>
                </div>
                <div class="input-group-append">
                    <button class="input-group-text fa  fa-close" @click="page=1;clearForm();" title="Очистить">&nbsp;</button>
                </div>
                <div class="input-group-append">
                    <button class="input-group-text fa fa-cog" @click="filtersSettings = !filtersSettings" title="Расширенный поиск">&nbsp;</button>
                </div>
            </div>
            <div v-if="filtersSettings" v-cloak class="row">
                <div class="col-3 text-nowrap">
                    Дата<br/>
                    <div style="display: inline-block;width: 120px;"><date-picker :format="'DD.MM.YYYY'" v-model="xSearchDateMin" :first-day-of-week="1"></date-picker></div>
                    -
                    <div style="display: inline-block;width: 120px;"><date-picker :format="'DD.MM.YYYY'" v-model="xSearchDateMax" :first-day-of-week="1"></date-picker></div>
                </div>
                <div class="col-4">
                    Категория
                    <v-select :options="categoriesSelect" :clearable="true" :multiple="true" v-model="searchForm.categories">
                        <template #option="{ code, label, user_id }">
                            <span v-if="!!user_id" class="text-success">@{{ label }}</span>
                            <template v-else>@{{ label }}</template>
                        </template>
                    </v-select>
                </div>
                <div class="col-3">
                    Сумма<br/>
                    <input type="text" class="form-control" v-model="searchForm.summMin" style="width: 100px;display: inline-block;"/>
                    -
                    <input type="text" class="form-control" v-model="searchForm.summMax" style="width: 100px;display: inline-block;"/>
                </div>
                <div class="col-2">
                    Ежемесячная<br/>
                    <select class="form-control" v-model="searchForm.type" @change="page=1;load();">
                        <option value="">...</option>
                        <option value="0">Нет</option>
                        <option value="1">Да</option>
                    </select>
                </div>
            </div>
            <hr/>
        </div>

        <div class="row mb-2" v-cloak>
            <div class="col">
                <span v-if="summ.total != summ.income && summ.total != summ.outcome">Остаток: @{{ summ.total }}</span>
                <span v-if="summ.income" class="ml-1 text-success">Доходы: @{{ summ.income }}</span>
                <span v-if="summ.outcome" class="ml-1 text-danger">Расходы: @{{ summ.outcome }}</span>
            </div>
            {{--
            <div class="col">За последний месяц: @{{ summ }}</div>
            <div class="col">За последнюю неделю: @{{ summ }}</div>
            --}}
            <div class="col">
                <button class="ml-1 btn btn-info float-right fa fa-2x fa-line-chart" :class="{active: viewType == 'graph'}" @click="viewType = 'graph'; load();" aria-hidden="true"></button>
                <button class="ml-1 btn btn-info float-right fa fa-2x fa-table" :class="{active: viewType == 'stat'}" @click="viewType = 'stat'; load();" aria-hidden="true"></button>
                <button class="btn btn-info float-right fa fa-2x fa-list" :class="{active: viewType == 'operations'}" @click="viewType = 'operations'" aria-hidden="true"></button>
            </div>
        </div>
        <div v-if="catSettings" v-cloak class="mt-2">
            <div>
                <hr/>
                <div>Здесь вы можете управлять своими категориями <a href="javascript:void(0)" @click="catSettings = false" class="float-right fa fa-close">закрыть</a></div>
                <div v-for="(cat,i) in categories" class="input-group" v-if="!!cat.user_id">
                    <input class="form-control" v-model="categories[i].name"/>
                    <div class="input-group-append">
                        <button class="input-group-text fa fa-save"  title="Сохранить" @click="catSave(cat.id)">&nbsp;</button>
                    </div>
                    <div class="input-group-append">
                        <button class="input-group-text fa fa-trash"  title="Удалить" @click="catDel(cat.id)">&nbsp;</button>
                    </div>
                </div>
                <div class="input-group">
                    <input class="form-control" v-model="newCategory"/>
                    <div class="input-group-append">
                        <button class="input-group-text fa fa-save"  title="Добавить" @click="catStore">&nbsp;</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Спиннер --}}
        <div class="forLoading" v-if="tableLoading">
            <div class="loading"></div>
        </div>

        {{-- Таблица --}}
        <div class="" v-if="viewType == 'operations'" v-cloak>
            <table class="rwd-table" v-if="!!operations.data">
                <thead>
                <tr>
                    {{--<th scope="col">Id</th>--}}
                    <th scope="col">Дата</th>
                    <th scope="col">Сумма</th>
                    <th scope="col">
                        Категория
                        <button class="input-group-text fa fa-cog" @click="catSettings = !catSettings" title="Настройки категорий">&nbsp;</button>

                    </th>
                    <th scope="col">Комментарий</th>
                    <th scope="col">Тэги</th>
                    <th scope="col">Ежемесячная</th>
                    <th scope="col">Действие</th>
                </tr>
                </thead>
                <tbody>
                    {{-- Новая операция --}}
                    <tr>
                        <td data-th="Дата">
                            <date-picker v-model="xNewDate" :format="'DD.MM.YYYY'" first-day-of-week="1"></date-picker>
                        </td>
                        <td data-th="Сумма">
                            <input type="text" class="form-control" v-model="newItem.summ" @keyup.enter="storeRow('{{ route('dashboard.store') }}')"/>
                        </td>
                        <td data-th="Категория">
                           {{-- <input type="text" class="form-control" v-model="newItem.category_id"/>--}}
                            <v-select :options="categoriesSelect" v-model="xNewCategory" :clearable="false">
                                <template #option="{ code, label, user_id }">
                                    <span v-if="!!user_id" class="text-success">@{{ label }}</span>
                                    <template v-else>@{{ label }}</template>
                                </template>
                            </v-select>
                        </td>
                        <td data-th="Комментарий">
                            <input type="text" class="form-control"  v-model="newItem.comment" @keyup.enter="storeRow('{{ route('dashboard.store') }}')"/>
                        </td>
                        <td data-th="Тэги">
                            <input type="text" class="form-control"  v-model="newItem.tags" @keyup.enter="storeRow('{{ route('dashboard.store') }}')"/>
                        </td>
                        <td data-th="Ежемесячная">
                            <input type="checkbox"  v-model="newItem.type" value="1"/>
                        </td>
                        <td>
                            <a href="javascript:void(0);" @click="storeRow('{{ route('dashboard.store') }}')" class="text-success fa fa-2x fa-save" title="Добавить"></a>
                            <a href="javascript:void(0);" class="ml-1 text-danger fa fa-2x fa-trash" @click="clearNewItem()" title="Очистить"></a>
                        </td>
                    </tr>

                    {{-- Список операций --}}
                    <tr v-for="item in operations.data" v-on:dblclick="editRow(item.id)" v-bind:class="{'text-success': item.summ > 0}">
                        {{--<td>{{ $item->id }}</td>--}}
                        <td data-th="Дата" class="date">
                            <template v-if="edit != item.id">
                                <span @click="newItem.date = item.date">@{{ dateConvert(item.date) }}</span>
                            </template>
                            <template v-else>
                               <date-picker v-model="xEditDate" :format="'DD.MM.YYYY'" first-day-of-week="1"></date-picker>
                            </template>
                        </td>
                        <td data-th="Сумма" class="summ">
                            <template v-if="edit != item.id">
                                <span @click="newItem.summ = item.summ">@{{ item.summ }}</span>
                            </template>
                            <template v-else>
                                <input type="text" class="form-control" v-model="editItem.summ" @keyup.enter="saveRow('{{ route('dashboard.update') }}', item.id)"/>
                            </template>
                        </td>
                        <td data-th="Категория" @click="newItem.category_id = item.category_id">
                            <template v-if="edit != item.id">
                                <span>
                                    <a href="javascript:void(0);"
                                       @click="searchForm.categories = [{code: item.category_id, label: categoriesAssoc[item.category_id]}];filtersSettings=true;page=1;load();"
                                    >@{{ categoriesAssoc[item.category_id] }}</a>
                                </span>
                                <i v-if="item.category_id" class="fa fa-copy" @click="newItem.category_id = item.category_id" style="opacity: .3;"></i>
                            </template>
                            <template v-else>
                                <v-select :options="categoriesSelect" v-model="xEditCategory" :clearable="false"></v-select>
                            </template>
                        </td>
                        <td data-th="Комментарий">
                            <template v-if="edit != item.id">
                                <span @click="newItem.comment = item.comment">@{{ item.comment }}</span>
                            </template>
                            <template v-else>
                                <input type="text" class="form-control" v-model="editItem.comment" @keyup.enter="saveRow('{{ route('dashboard.update') }}', item.id)"/>
                            </template>
                        </td>
                        <td data-th="Тэги">
                            <template v-if="edit != item.id">
                                <template v-for="tag in item.tags.split(' ')">
                                    <a href="javascript:void(0);" @click="searchTag(tag)" class="mr-1">@{{ tag }}</a>
                                    <i v-if="item.tags" class="fa fa-copy" @click="newItem.tags = item.tags" style="opacity: .3;"></i>
                                </template>
                            </template>
                            <template v-else>
                                <input type="text" class="form-control" v-model="editItem.tags" @keyup.enter="saveRow('{{ route('dashboard.update') }}', item.id)"/>
                            </template>
                        </td>
                        <td data-th="Ежемесячная" @click="newItem.type = item.type">

                            <template v-if="edit != item.id">
                                <i v-if="item.type" class="fa fa-check" ></i>
                            </template>
                            <template v-else>
                                <input type="checkbox" v-model="editItem.type" value="1"/>
                            </template>
                        </td>

                        <td data-th="Действие">
                            <template v-if="edit != item.id">
                                <a href="javascript:void(0);" @click="editRow(item.id)" class="text-info fa fa-edit fa-2x" title="Изменить"></a>
                                <a href="javascript:void(0);" @click.stop.prevent="removeRow('{{ route('dashboard.delete') }}', item.id)"
                                   class="ml-1 text-danger fa fa-2x fa-trash" title="Удалить"></a>
                            </template>
                            <template v-else>
                                <a href="javascript:void(0);" @click="saveRow('{{ route('dashboard.update') }}', item.id)"
                                   class="text-success fa fa-2x fa-save" title="Сохранить"></a>
                                <a href="javascript:void(0);" @click="edit = 0" class="ml-1 fa fa-2x fa-mail-reply" title="Отмена"></a>
                            </template>
                        </td>

                    </tr>

                </tbody>
            </table>

            {{-- $incomes->links() --}}
            <nav v-if="operations.data.length">
                <ul class="pagination float-left w-75" v-if="operations.last_page != 1" >
                    <li class="page-item" v-if="operations.current_page > 1"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(1);">&laquo;</a></li>
                    <li class="page-item" v-if="operations.current_page > 3"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(1);">1</a></li>
                    <li class="page-item" v-if="operations.current_page > 3"><a href="javascript:void(0);" class="page-link">...</a></li>
                    <li class="page-item" v-if="operations.current_page > 2"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.current_page - 2);">@{{ operations.current_page - 2 }}</a></li>
                    <li class="page-item" v-if="operations.current_page > 1"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.current_page - 1);">@{{ operations.current_page - 1 }}</a></li>
                    <li class="page-item active"><a href="javascript:void(0);" class="page-link active" v-on:click.prevent="setPage(operations.current_page);">@{{ operations.current_page }}</a></li>
                    <li class="page-item" v-if="operations.current_page < operations.last_page - 1"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.current_page + 1);">@{{ operations.current_page + 1 }}</a></li>
                    <li class="page-item" v-if="operations.current_page < operations.last_page - 2"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.current_page + 2);">@{{ operations.current_page + 2 }}</a></li>
                    <li class="page-item" v-if="operations.current_page < operations.last_page - 3"><a href="javascript:void(0);" class="page-link">...</a></li>
                    <li class="page-item" v-if="operations.current_page < operations.last_page"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.last_page);">@{{ operations.last_page }}</a></li>
                    <li class="page-item" v-if="operations.current_page < operations.last_page"><a href="javascript:void(0);" class="page-link" v-on:click.prevent="setPage(operations.last_page);">&raquo;</a></li>
                </ul>

                <select class="form-control float-right w-25" style="max-width: 100px" v-model="limit" @change="page=1;load();">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </nav>
            <div v-else>По вашему запросу ничего не найдено</div>

        </div>

        {{-- кнопки группировки для статистики и графика --}}
        <div v-if="viewType == 'stat' || viewType == 'graph'">

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-info" :class="{active: groupType == 'd'}" @click="changeGroupType('d')">День</button>
                <button type="button" class="btn btn-info" :class="{active: groupType == 'm'}" @click="changeGroupType('m')">Месяц</button>
                <button type="button" class="btn btn-info" :class="{active: groupType == 'y'}" @click="changeGroupType('y')">Год</button>
            </div>

            {{-- Статистика --}}
            <div v-if="viewType == 'stat'" v-cloak>
                <div class="table-responsive mt-2" v-if="stat.length">
                    <table class="table">
                        <tr>
                            <th>Дата</th>
                            <th>Расход</th>
                            <th>Доход</th>
                            <th>Остаток</th>
                        </tr>
                        <tr v-for="item in stat">
                            <td data-th="Дата">
                                <a href="javascript:void(0)" v-html="dateConvert(item.group)" @click="searchGroup(item.group)"></a>
                            </td>
                            <td data-th="Расход" v-html="item.outcome"></td>
                            <td data-th="Доход" v-html="item.income"></td>
                            <td data-th="Остаток" v-html="item.total"></td>
                        </tr>
                    </table>
                </div>
                <div v-else-if="!tableLoading">По вашему запросу ничего не найдено</div>
            </div>

            {{-- График --}}
            <div v-if="viewType == 'graph'">
                <div id="container"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/data.js"></script>
    <script src="https://code.highcharts.com/stock/modules/drag-panes.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <script>
        var loadUrl = '{{ route('dashboard.index') }}';
        var curDate = '{{ date('Y-m-d') }}';
    </script>
@endpush
