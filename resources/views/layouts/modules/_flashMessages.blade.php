@if(session('flash_message') && isset(session('flash_message')['type']) && isset(session('flash_message')['text']))
    <div class='container mt-3'>
        <div class="row no-gutters justify-content-center">
            <div class="text-center col-lg-12 mb-0 alert alert-{{ session('flash_message')['type'] }}">
                {!!  session('flash_message')['text']!!}
            </div>
        </div>
    </div>
@endif
