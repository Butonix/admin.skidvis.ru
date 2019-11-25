const CHECKBOXES_CLASS = '.check-all';

export default class Checkboxes {
    constructor(element) {
        this.checkboxBody = element;
        this.checkbox = $(this.checkboxBody).find('input[type=checkbox]')[0];

        this.initial();
        this.initializeEvents();
    }

    checking() {
        let upanelCountInputs = $('input:checkbox').not(this.checkbox),
            upanelCountCheckedInputs = $('input:checkbox:checked').not(this.checkbox);

        if (upanelCountInputs.length === upanelCountCheckedInputs.length) {
            $(this.checkbox).prop('checked', 'checked');
        } else {
            $(this.checkbox).prop('checked', false);
        }
    }

    initial() {
        this.checking();
    }

    initializeEvents() {
        $(this.checkboxBody)
            .off('click')
            .on('click', (event) => {
                if ($(event.target).hasClass('btn')) {
                    $(this.checkbox).trigger('click');
                    $('input:checkbox').prop('checked', this.checkbox.checked);
                } else if ($(event.target).hasClass('form-check-input')) {
                    $('input:checkbox').not(this.checkbox).prop('checked', this.checkbox.checked);
                }
            });

        $('input[type=checkbox]').not(this.checkbox)
            .off('click')
            .on('click', (event) => {
                this.checking();
            });
    }

    static init() {
        $(CHECKBOXES_CLASS).each(function (index, DOMElement) {
            new Checkboxes(DOMElement);
        })
    }
}

