const CHANGE_SUBMIT_CLASS = '.onChangeSubmit';

export default class ChangeSubmit {
    constructor(element) {
        this.form = element;

        this.initializeEvents();
    }

    initializeEvents() {
        this.form
            .off('change')
            .on('change', (event) => {
                this.form.submit();
            });
    }

    static init() {
        $(CHANGE_SUBMIT_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new ChangeSubmit(element);
        });
    }
}

