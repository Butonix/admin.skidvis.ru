import $ from 'jquery';

const loadModuleDOM =
    `<div id='loadModuleImage'>
        <div id='loadModuleImage__spinner'>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
     </div>`;

export function enableLoadModule(parent) {
    let loadModule = parent.find('.load-module-image');
    loadModule.append(loadModuleDOM);
    loadModule = $('#loadModuleImage');
    loadModule.css({
        'display': 'flex'
    });
}

export function disableLoadModule(parent) {
    let loadModule = $('#loadModuleImage');
    loadModule.css({
        'display': 'none'
    });
    loadModule = parent.find('.load-module-image');
    loadModule.empty();
}
