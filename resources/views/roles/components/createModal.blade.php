<div class="modal modal-reload-page fade" id="roles-create-modal" tabindex="-1" role="dialog" aria-labelledby="roles-create-modal-Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roles-create-modal-label">Создание роли</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                {!! Form::model(null, ['url' => route('roles.store'), 'method' => 'POST', 'class' => 'onSubmitAjax']) !!}
                @include('roles.components._form')
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
