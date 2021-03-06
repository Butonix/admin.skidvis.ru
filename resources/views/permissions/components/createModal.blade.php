<div class="modal modal-reload-page fade" id="permissions-create-modal" tabindex="-1" role="dialog" aria-labelledby="permissions-create-modal-Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissions-create-modal-label">Создание разрешения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                {!! Form::model(null, ['url' => route('permissions.store'), 'method' => 'POST', 'class' => 'onSubmitAjax']) !!}
                @include('permissions.components._form', [
                    'canCreateCRUD' => true
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
