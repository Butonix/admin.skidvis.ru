<div class="js-index">
    @include('social.networks.filters._filters', [
        'frd' => $frd
    ])
    <div class="container pt-lg-3 pb-lg-5 px-0">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class='row no-gutters'>
                    <div class="col-lg-auto mb-lg-2 mt-lg-2 mr-lg-2 col-md-auto mb-md-2 mt-md-2 mr-md-2 col-sm-auto mb-sm-2 mt-sm-2 mr-sm-2 col-auto mb-2 mt-2 mr-2">
                        <div class='btn-group' role='group'>
                            <a href='#' class='btn btn-outline-secondary check-all'>
                                @include('forms._checkbox',[
									'name' => ' ',
									'checked' => false,
								])
                            </a>
                            <button type="submit" class="btn btn-danger px-3 js-delete-elements" style='padding-top: .475rem;' data-name-elements='social_networks'
                                    data-tooltip='true' data-placement='bottom' title='Удалить'
                                    data-form-action='{{ route('social-networks.actions.destroy', $frd) }}' data-action-method='DELETE'>
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
                                <a href='{{ route('social-networks.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                   data-tooltip='true' data-placement='bottom' title='Новая соц.сеть'>
                                    Добавить <i class="fas fa-users"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-group">
                    @forelse ($socialNetworks as $socialNetwork)
                        <div class="list-group-item list-group-item-action">
                            <div class="row" id='social_network-{{ $socialNetwork->getKey() }}'>
                                <label class="col-lg-auto form-check-label d-flex align-items-center">
                                    @include('forms._checkbox',[
										'name' => 'social_networks[]',
										'value' => $socialNetwork->getKey(),
										'label' => '<span class="mr-2 logo--small"><img src="' . $socialNetwork->getIconUrl() . '"></span>' . $socialNetwork->getName() . '<span class="text-muted ml-2 small">' . $socialNetwork->getDisplayName() . '</span>',
										'checked' => false,
									])
                                </label>
                                <div class="col-lg d-flex align-items-center justify-content-end">
                                    <form action='' style='display: none;'></form>
                                    <div class='btn-group' role='group'>
                                        <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                           href="{{ route('social-networks.show', $socialNetwork) }}" role='button' class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                           href="{{ route('social-networks.edit', $socialNetwork) }}" role='button' class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{ Form::open(['url' => route('social-networks.destroy', $socialNetwork), 'method' => 'delete', 'class' => 'btn btn-danger',
                                            'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
                                             'onclick' => 'event.preventDefault(); if (!confirm("Удалить соц.сеть?")) return; this.submit()']) }}
                                            <i class="far fa-trash-alt"></i>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>Соц.сети отсутствуют</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $socialNetworks,
		'frd' => $frd
	])
</div>
