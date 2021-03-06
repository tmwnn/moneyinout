@extends('layouts.cms')

@section('title', 'Категории')

@section('content')
    <div class="container">
        <div class="tab-pane mb-2">
            <a class="btn btn-primary btn-lg" href="javascript:void(0);" role="button" @click="add = 1;">Добавить</a>
            <a class="btn btn-primary btn-lg" href="{{ route('cms.categories.index') }}" role="button">Обновить</a>
            {{ Form::open(array('url' => route('cms.categories.index'), 'class' => 'btn')) }}
            {{ Form::text('name', $name) }}
            {{ Form::submit('Найти') }}
            {{ Form::close() }}
        </div>
        <div v-if="add" class="mb-2">
            <div class="form-group">
                {{ Form::label('Наименование') }}
                {{ Form::text('name', '',  ['ref' => 'name_0', 'class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('Наименование англ') }}
                {{ Form::text('name_eng', '',  ['ref' => 'name_eng_0', 'class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('Валюта') }}
                {{ Form::select('user_id', $users, '', ['ref' => 'currency_id_0', 'class' => 'form-control']) }}
            </div>
            <div class="form-group">
                <a class="btn btn-success" href="javascript:void(0);" @click="save('{{ route('cms.categories.store') }}', '0', ['name','name_eng','currency_id'])">Сохранить</a>
                <a class="btn btn-info" href="javascript:void(0);" @click="add = 0">Отмена</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped  table-bordered">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Наименование</th>
                    <th scope="col">Пользователь</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tbody>
                    @foreach($categories as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <template v-if="edit != '{{ $item->id }}'">
                                    {{ $item->name }}
                                </template>
                                <template v-else>
                                    {{ Form::text('name', $item->name, ['ref' => 'name_' .  $item->id]) }}
                                </template>
                            </td>
                            <td>
                                <template v-if="edit != '{{ $item->id }}'">
                                    {{ $item->user->name ?? '' }}
                                </template>
                                <template v-else>
                                    {{ Form::select('currency_id', $users, $item->user->id ?? '', ['ref' => 'user_id_' .  $item->id]) }}
                                </template>
                            </td>

                            <td>
                                <template v-if="edit != '{{ $item->id }}'">
                                    <a href="javascript:void(0);" @click="edit = '{{ $item->id }}'" class="btn btn-info">Изменить</a>
                                    <a href="javascript:void(0);" @click.stop.prevent="remove('{{ route('cms.categories.delete') }}', '{{ $item->id }}')" class="btn btn-danger">Удалить</a>
                                </template>
                                <template v-else>
                                    <a href="javascript:void(0);" @click="save('{{ route('cms.categories.update') }}', '{{ $item->id }}', ['name','user_id'])" class="btn btn-success" >Сохранить</a>
                                    <a href="javascript:void(0);" @click="edit = 0" class="btn btn-info" >Отмена</a>
                                </template>


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $categories->links() }}
        </div>

    </div>
@endsection
