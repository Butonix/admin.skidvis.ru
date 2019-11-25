/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');
require('../libs/farbtastic/farbtastic');
require('jquery-mask-plugin/dist/jquery.mask.min');
require('suggestions-jquery/dist/js/jquery.suggestions.min');
require('@dashboardcode/bsmultiselect/dist/js/BsMultiSelect.min');
require('dropzone/dist/dropzone');

// window.Vue = require('vue');
window.toastr = require('toastr/build/toastr.min');
require('./settings/toastr');
require('lazyload/lazyload.min');

import {createPhoneMasks} from './inits/jquery-phone-mask';
import {initializeTooltips} from './inits/tooltips';
import {initialize} from './initialize';
import {findAddresses} from './inits/findAddresses';
import {findOrganizations} from './inits/findOrganizations';
import {enableLazyLoad} from './inits/lazyload';
import CategoriesTypesChange from './inits/categoriesTypesChange';
import SetPointSchedule from './inits/setPointSchedule';
import ImagesDownloadPreview from './inits/imagesDownloadPreview';
import TextEditor from './inits/textEditor';

createPhoneMasks();
initializeTooltips();
findAddresses();
findOrganizations();
enableLazyLoad();
initialize();
CategoriesTypesChange.init();
SetPointSchedule.init();
ImagesDownloadPreview.init();
TextEditor.init();
$('body').tooltip({selector: '[data-tooltip="true"]'});

$(document).on('click', '#find-fields-open-button', function (e) {
    e.preventDefault();
    $('.right-menu__body').toggleClass('active');
});

$(document).on('click', '.js-right-menu__close-button', function (e) {
    e.preventDefault();
    $('.right-menu__body').toggleClass('active');
});

$("select[multiple='multiple']").bsMultiSelect();
$('.carousel').carousel();

$(document).on('submit', 'form', function () {
   $('.js-mask-phone').unmask();
});
$('.js-color-picker').each(function () {
  let $th = $(this)
  let index = $th.data('index')
  $th.farbtastic(`.js-color-input[data-index=${index}]`);
})
