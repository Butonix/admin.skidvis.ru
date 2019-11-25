import {initialize} from '../initialize';

const MAKE_READ_NOTIFICATION_SELECTOR = '.js-make-read';

export default class MakeReadNotification {
    constructor(element) {
        this.element = element;
        this.makeReadUrl = this.element.data('url');
        this.makeReadMethod = this.element.data('method');

        this.initializeEvents();
    }

    makeAsReadClass() {
        this.element.removeClass('notification--unread');
        this.element.removeClass('js-make-read');
    }

    sendAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:        this.makeReadMethod,
            url:         this.makeReadUrl,
            async:       true,
            success:     (data) => {
                initialize();
            },
            error:      function (data) {
                console.log('Ajax error:', data);
            }
        });
    }

    initializeEvents() {
        this.element.off('mouseenter')
            .on('mouseenter', (event) => {
                if (this.element.hasClass('notification--unread')) {
                    console.log('sendAJAX');
                    this.sendAjax();
                }

                this.makeAsReadClass();
            });
    }

    static init() {
        $(MAKE_READ_NOTIFICATION_SELECTOR).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new MakeReadNotification(element);
        });
    }
}
