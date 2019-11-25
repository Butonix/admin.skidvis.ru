const SWITCH_ORDERING_CLASS = '.js-switch-ordering';

export default class SwitchOrdering {
    constructor (element) {
        this.element = element;

        this.initializeEvents();
    }

    resetOrdering() {
        let anotherOrderingElements = $(SWITCH_ORDERING_CLASS).not(this.element);
        anotherOrderingElements.find('option:selected').prop('selected', false);
        anotherOrderingElements.find('option').first().prop('selected', false);

        $('input[name=ordering]').val(this.element.attr('name'));
    }

    initializeEvents() {
        this.element
            .off('change')
            .on('change', (event) => {
                event.preventDefault();
                // event.stopPropagation();
                this.resetOrdering();
            });
    }

    static init() {
        $(SWITCH_ORDERING_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new SwitchOrdering(element);
        })
    }
}
