export default class StrikeTool {
    static get isInline() {
        return true;
    }

    get state() {
        return this._state;
    }

    set state(state) {
        this._state = state;

        this.button.classList.toggle(this.api.styles.inlineToolButtonActive, state);
    }

    constructor({api}) {
        this.api = api;
        this.button = null;
        this._state = false;

        this.tag = 'S';
        this.class = 'cdx-strike';
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.innerHTML = '<i class="fas fa-strikethrough"></i>';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.classList.add(this.api.styles.inlineToolButton + '--strike');

        return this.button;
    }

    surround(range) {
        if (this.state) {
            this.unwrap(range);
            return;
        }

        this.wrap(range);
    }

    wrap(range) {
        const selectedText = range.extractContents();
        const strike = document.createElement(this.tag);

        strike.classList.add(this.class);
        strike.appendChild(selectedText);
        range.insertNode(strike);

        this.api.selection.expandToTag(strike);
    }

    unwrap(range) {
        const strike = this.api.selection.findParentTag(this.tag, this.class);
        const text = range.extractContents();

        strike.remove();

        range.insertNode(text);
    }

    checkState(selection) {
        const strike = this.api.selection.findParentTag(this.tag);

        this.state = !!strike;
    }

    static get sanitize() {
        return {
            s: {
                class: 'cdx-strike'
            }
        };
    }
}
