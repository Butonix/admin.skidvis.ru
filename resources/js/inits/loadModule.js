import $ from 'jquery';

const loadModuleDOM =
    `<div id='loadModule'>
        <div id='loadModule__spinner'>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
     </div>`;

export function enableLoadModule() {
    let loadModule = $('#loadModule');
    if (0 === loadModule.length) {
        $('body').append(loadModuleDOM);
        loadModule = $('#loadModule');
    }
    loadModule.css({
        'display': 'flex'
    });
}

export function disableLoadModule() {
    let loadModule = $('#loadModule');
    loadModule.css({
        'display': 'none'
    });
}
