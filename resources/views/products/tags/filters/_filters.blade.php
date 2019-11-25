<div class="container px-0">
    <div id='find-fields' class="row justify-content-center">
        <div class="col-lg-12">
            @include('products.tags.filters.filters', [
                'frd' => $frd
            ])
        </div>
    </div>
    <div id='find-fields--right-menu' class='right-menu row justify-content-center'>
        <div class='right-menu__body px-2 py-5'>
            <div class='right-menu__body-close js-right-menu__close-button pr-2 pt-2'>
                <i class="fas fa-times fa-2x"></i>
            </div>
            @include('products.tags.filters.filters', [
                'frd' => $frd
            ])
        </div>
    </div>
</div>
