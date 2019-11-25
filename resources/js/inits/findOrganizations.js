import $ from 'jquery';

export function findOrganizations() {
    $('.js-find-organizations:not([readonly])').suggestions({
        token:    '1824220a034db4dce4b2bdd0d23282b53c6c1c49',
        type:     'PARTY',
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function (suggestion) {
            console.log(suggestion);
            $('[name=inn]').val(suggestion.data.inn);
            $('[name=orgnip]').val(suggestion.data.ogrn);
            $('[name=okved]').val(suggestion.data.okved);
            $('[name=name]').val(suggestion.value);
            $('[name=address]').val(suggestion.data.address.unrestricted_value);
            $('[name=latitude]').val(suggestion.data.address.data.geo_lat);
            $('[name=longitude]').val(suggestion.data.address.data.geo_lon);
        }
    });
}
