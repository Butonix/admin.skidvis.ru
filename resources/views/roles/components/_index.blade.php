<div class="js-index">
    @include('roles.filters._filters', [
        'frd' => $frd
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class='row no-gutters'>
                    <div class="col-lg-auto mb-lg-2 mt-lg-2 mr-lg-2 col-md-auto mb-md-2 mt-md-2 mr-md-2 col-sm-auto mb-sm-2 mt-sm-2 mr-sm-2 col-auto mb-2 mt-2 mr-2">
                        <div class='btn-group' role='group'>
                            <a href='#' class='btn btn-outline-secondary check-all'>
                                @include('forms._checkbox',[
									'name' => ' ',
									'checked' => false,
								])
                            </a>
                            <button type="submit" class="btn btn-danger px-3 js-delete-elements" style='padding-top: .475rem;' data-name-elements='roles'
                                    data-tooltip='true' data-placement='bottom' title='Удалить'
                                    data-form-action='{{ route('roles.actions.destroy', $frd) }}' data-action-method='DELETE'>
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg mb-lg-2 mt-lg-2 col-md mb-md-2 mt-md-2 col-sm mb-sm-2 mt-sm-2 col mb-2 mt-2">
                        <div class='row justify-content-end'>
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pr-lg-2 pr-1">
                                <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                        data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                <button class="btn btn-success" style='padding-top: .475rem;'  data-toggle="modal" data-target="#roles-create-modal"
                                        data-tooltip='true' data-placement='bottom' title='Новая роль'>
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-group">
                    @forelse ($roles as $role)
                        <div class="list-group-item list-group-item-action">
                            <div class="row">
                                <label class="col-lg-5 form-check-label d-flex align-items-center">
                                    @include('forms._checkbox',[
										'name' => 'roles[]',
										'value' => $role->getKey(),
										'label' => $role->getDisplayName(),
										'checked' => false,
									])
                                </label>
                                <p class="col-lg mb-lg-0 d-flex align-items-center">
                                    <a href="{{ route('roles.show', $role) }}">{{ $role->getName() }}</a>
                                </p>
                                <div class="col-lg d-flex justify-content-end">
                                    <div class='btn-group' role='group'>
                                        <a data-tooltip="true" data-placement="bottom" title="Разрешения" role='button'
                                           class="btn btn-outline-secondary" href='{{ route('roles.permissions', $role) }}'>
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary"
                                           data-tooltip="true" data-placement="bottom" title="Редактирование" role='button'>
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No roles</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $roles,
		'frd' => $frd
	])
</div>
