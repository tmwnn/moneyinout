@extends('layouts.base')

@push('scripts')
    <script type="text/javascript" src="/js/qrcode.min.js"></script>
    <script type="text/javascript">
        var telegramUrl = '{{ $telegramUrl ?? '' }}';
    </script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Настройки профиля</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Имя</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right"></label>
                            <div class="col-md-6">
                                <a href="javascript:void(0);" @click="changePassword = !changePassword">Сменить пароль</a>
                            </div>
                        </div>

                        <template v-if="changePassword || '@error('password') 1 @enderror'">
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Подтверждение пароля</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right"></label>
                                <div class="col-md-6">
                                    <a href="javascript:void(0);" class="btn btn-info" v-on:click.prevent="changePassword = false">Отмена</a>
                                </div>
                            </div>
                        </template>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right"></label>
                            <div class="col-md-6">
                                @if(empty($telegram_chat_id))
                                    <a href="javascript:void(0);" @click="messengerTie">Привязать Telegram-аккаунт</a>
                                @else
                                    <a href="javascript:void(0);" @click="messengerUnTie">Отвязать Telegram-аккаунт</a>
                                @endif
                                <br/>
                                <input type="hidden" name="telegram_chat_id" id="telegram_chat_id" value="{{ $telegram_chat_id }}" class="input"/>

                                    <div v-show="connectTelegram" class="top_20" v-cloak>
                                        <div class="cb_modal_window cb_modal_access_req_window">
                                            {{--<a href="javascript:void(0);" class="modal_close close js_modal_close">×</a>--}}
                                            <div class="cb_modal_body">
                                                <div class="cb_indent_top_20">
                                                    <ul class="messenger_modal_list" style="list-style-type:disc;">
                                                        <li>
                                                            <p id="messenger_qr_code" v-html="messenger_qr_code"></p>
                                                            <div id="qrcode" style="width: 255px; margin:0 auto;"></div>
                                                        </li>
                                                        <li>
                                                            <p id="install_messenger_link" v-html="install_telegram_message"></p>
                                                        </li>
                                                        <li>
                                                            <span id="open_messenger_msg" v-html="open_messenger_msg"></span>
                                                            <a :href="messengerUrl" target="_blank" @click="messengerTie">ссылке</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="btn btn-info" v-on:click.prevent="connectTelegram = false">Отмена</a>
                                    </div>
                            </div>


                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
