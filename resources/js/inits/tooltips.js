import $ from 'jquery';

export function initializeTooltips() {
    $("body").tooltip({selector: '[data-tooltip="true"]', trigger : 'hover'});
}
