@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Подвердите ваш Email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            На ваш адрес электронной почты отправлена новая ссылка для подтверждения
                        </div>
                    @endif

                        Прежде чем продолжить, проверьте свою электронную почту на наличие ссылки для подтверждения.
                        Если вы не получили письмо, <a href="{{ route('verification.resend') }}">нажмите здесь, чтобы запросить письмо повторно</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
