const RESET_MODALS_CLASS = '.modal';

export default class ResetModals {
    constructor(element) {
        this.modal = element;

        this.initializeEvents();
    }

    initializeEvents() {
        this.modal
            .off('hidden.bs.modal')
            .on('hidden.bs.modal', (event) => {
                this.modal.find('form').trigger('reset');
            });
    }

    static init() {
        $(RESET_MODALS_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new ResetModals(element);
        });
    }
}
