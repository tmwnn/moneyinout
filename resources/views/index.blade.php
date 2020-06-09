@extends('layouts.base')

@section('title', __('menu.main'))

@section('content')
    <div class="container">

        <div class="tab-pane mb-2">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Что ищем" aria-label="Что ищем" aria-describedby="basic-addon2" v-model="searchString">
                <div class="input-group-append">
                    <button class="input-group-text fa  fa-search" @click="load" title="Найти">&nbsp;</button>
                </div>
                <div class="input-group-append">
                    <button class="input-group-text fa  fa-close" @click="searchString = '';load();" title="Очистить">&nbsp;</button>
                </div>
                <div class="input-group-append">
                    <button class="input-group-text fa fa-cog" title="Расширенный поиск">&nbsp;</button>
                </div>
            </div>
        </div>

        <div class="row mb-2" v-cloak>
            <div class="col">Сумма: @{{ summ }}</div>
            {{--
            <div class="col">За последний месяц: @{{ summ }}</div>
            <div class="col">За последнюю неделю: @{{ summ }}</div>
            --}}
            <div class="col">
                <button class="ml-1 btn btn-info float-right fa fa-2x fa-line-chart" aria-hidden="true"></button>
                <button class="btn btn-info active float-right fa fa-2x fa-table" aria-hidden="true"></button>
            </div>
        </div>

        <div class="table-responsive" v-cloak>

            <table class="rwd-table" v-if="!!operations.data">
                <thead>
                <tr>
                    {{--<th scope="col">Id</th>--}}
                    <th scope="col">Дата</th>
                    <th scope="col">Сумма</th>
                    <th scope="col">Категория</th>
                    <th scope="col">Комментарий</th>
                    <th scope="col">Тэги</th>
                    <th scope="col">Действие</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-th="Дата">
                            <date-picker v-model="xNewDate" :format="'DD.MM.YYYY'"></date-picker>
                        </td>
                        <td data-th="Сумма">
                            <input type="text" class="form-control" v-model="newItem.summ"/>
                        </td>
                        <td data-th="Категория">
                           {{-- <input type="text" class="form-control" v-model="newItem.category_id"/>--}}
                            <v-select :options="categoriesSelect" v-model="xNewCategory" :clearable="false"></v-select>
                        </td>
                        <td data-th="Комментарий">
                            <input type="text" class="form-control"  v-model="newItem.comment"/>
                        </td>
                        <td data-th="Тэги">
                            <input type="text" class="form-control"/>
                        </td>
                        <td>
                            <a href="javascript:void(0);" @click="storeRow('{{ route('dashboard.store') }}')" class="text-success fa fa-2x fa-save" title="Добавить"></a>
                            <a href="javascript:void(0);" class="ml-1 text-danger fa fa-2x fa-trash" title="Очистить"></a>
                        </td>
                    </tr>
                    <tr v-for="item in operations.data" v-on:dblclick="editRow(item.id)" v-bind:class="{'text-success': item.summ > 0}">
                        {{--<td>{{ $item->id }}</td>--}}
                        <td data-th="Дата" class="date">
                            <template v-if="edit != item.id">
                                <span @click="newItem.date = item.date">@{{ dateConvert(item.date) }}</span>
                            </template>
                            <template v-else>
                               <date-picker v-model="xEditDate" :format="'DD.MM.YYYY'"></date-picker>
                            </template>
                        </td>
                        <td data-th="Сумма" class="summ">
                            <template v-if="edit != item.id">
                                <span @click="newItem.summ = item.summ">@{{ item.summ }}</span>
                            </template>
                            <template v-else>
                                <input type="text" class="form-control" v-model="editItem.summ" />
                            </template>
                        </td>
                        <td data-th="Категория">
                            <template v-if="edit != item.id">
                                <span @click="newItem.category_id = item.category_id">@{{ categoriesAssoc[item.category_id] }}</span>
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
                                <input type="text" class="form-control" v-model="editItem.comment"/>
                            </template>
                        </td>
                        <td data-th="Тэги">
                            <template v-if="edit != item.id">

                            </template>
                            <template v-else>
                                <input type="text" class="form-control" name="tags" />
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
            <nav>
                <ul class="pagination">
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
            </nav>

        </div>


    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <script>
        var loadUrl = '{{ route('dashboard.index') }}';
        var curDate = '{{ date('Y-m-d') }}';
    </script>
@endpush
