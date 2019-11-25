const FORM_VALIDATION_CLASS = '.js-form-validation';

export default class FormValidation {
    constructor(element) {
        this.form = element;

        this.initializeEvents();
    }

    validate() {
        console.log('123123');
        console.log(this.form.find(['[required]']));
        let data = this.form.serializeArray();
        _.forEach(data, function (element) {
           console.log(element['name'])
        });
    }

    initializeEvents() {
        this.form
            .off('submit')
            .on('submit', (event) => {
                event.preventDefault();
                event.stopPropagation();

                this.validate();
            });
    }

    static init() {
        console.log($(FORM_VALIDATION_CLASS));
        $(FORM_VALIDATION_CLASS).each(function (index, DOMElement) {
            // let element = $(DOMElement);
            // new FormValidation(element);
        })
    }
}
