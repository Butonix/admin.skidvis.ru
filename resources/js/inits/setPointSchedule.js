const SET_POINT_SCHEDULE_CLASS = '.js-set-point-schedule';

export default class SetPointSchedule {
    constructor(element) {
        this.element = element;

        if (this.element.val() === 'own_schedule' && this.element.prop('checked')) {
            $('.schedule__types.active').removeClass('active');
            $('.' + this.element.val()).addClass('active');
            $('.schedule__day-type').show();
        }

        this.initializeEvents();
    }

    initializeEvents() {

        this.element
            .off('click')
            .on('click', (event) => {
                $('.schedule__types.active').removeClass('active');
                $('.' + this.element.val()).addClass('active');

                if (this.element.val() === 'organization_schedule') {
                    // $('input[name$=start], input[name$=end]').prop('readonly', true);
                    $('.schedule__day-type').hide();
                } else {
                    $('.schedule__day-type').show();
                    // $('input[name$=start], input[name$=end]').prop('readonly', false);
                }
            });
    }

    static init() {
        $(SET_POINT_SCHEDULE_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new SetPointSchedule(element);
        });
    }
}
