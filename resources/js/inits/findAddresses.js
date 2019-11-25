import $ from 'jquery';

export function findAddresses() {
    $(".js-find-addresses:not([readonly])").suggestions({
        token: '1824220a034db4dce4b2bdd0d23282b53c6c1c49',
        type: "ADDRESS",
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            console.log(suggestion);
            $('[name=street]').val(suggestion.data.street_with_type);
            $('[name=building]').val(suggestion.data.house);
            $('[name=latitude]').val(suggestion.data.geo_lat);
            $('[name=longitude]').val(suggestion.data.geo_lon);
        }
    });
}
