<div class="modal modal-reload-page fade" id="provider-create-modal" tabindex="-1" role="dialog" aria-labelledby="provider-create-modal-Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="provider-create-modal-label">Добавление сервиса авторизации</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                {!! Form::model(null, ['url' => route('auth-providers.store'), 'method' => 'POST', 'class' => 'onSubmitAjax']) !!}
                @include('auth.components._providerForm', [
                    'isEditableSlug' => true
                ])
                <div class="form-group text-right mb-lg-0">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            Сохранить
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
