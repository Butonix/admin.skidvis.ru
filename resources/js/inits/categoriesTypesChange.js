import {enableLoadModule, disableLoadModule} from './loadModule';
import {initialize} from '../initialize';

const CATEGORIES_TYPES_CHANGE_CLASS = '.js-categories-types-change';

export default class CategoriesTypesChange {
    constructor(element) {
        this.element = element;

        this.initializeEvents();
    }

    actionsWithForm(event) {
        if ($(event.target).prop('checked')) {
            $('.categories-images').addClass('active');
        } else {
            $('.categories-images').removeClass('active');
        }
        // $('.schedule__form.active').removeClass('active');
        // $('.' + this.value).addClass('active');
    }

    initializeEvents() {
        this.element
            .off('click')
            .on('click', (event) => {
                this.actionsWithForm(event);
            });
    }

    static init() {
        $(CATEGORIES_TYPES_CHANGE_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new CategoriesTypesChange(element);
        });
    }
}

