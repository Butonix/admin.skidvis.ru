<div class='js-index'>
    <div class='row justify-content-center mt-5'>
        <div class='col-lg-6'>
            <div class='row'>
                <div class='col-lg-12'>
                    <p class='mb-0'>Последние соц.сети:</p>
                </div>
            </div>
            <div class='row justify-content-between'>
                @forelse($socialNetworks as $socialNetwork)
                    <div class='col-lg-auto col-auto mt-3'>
                        <span class='px-3 py-2' style='background-color: #EEEEEE; border-radius: 115px'>
                            <img src='{{ $socialNetwork->getIconUrl() }}' alt='' class='mr-2' width='24' height='24'> {{ $socialNetwork->getName() }}
                        </span>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>
