<div class='js-index'>
    <div class='row justify-content-center'>
        <div class='col-lg-12'>
            <div class='row'>
                <div class='col-lg-12'>
                    <p class='mb-0'>20 последних созданных категорий:</p>
                </div>
            </div>
            <div class='row justify-content-start'>
                @forelse($categories as $category)
                    <div class='col-lg-auto col-auto mt-3'>
                        <span class='px-3 py-2' style='background-color: #EEEEEE; border-radius: 115px'>
                            {{ $category->getName() }}
                        </span>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>
