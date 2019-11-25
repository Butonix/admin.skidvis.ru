import $ from 'jquery';

export function createPhoneMasks() {
    $('.js-mask-phone').mask('+7 0000000000', {
        placeholder: '+7 __________'
    });
}
