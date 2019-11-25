export default class SmallTool {
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

        this.tag = 'SMALL';
        this.class = 'cdx-small';
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.innerText = 'small';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.classList.add(this.api.styles.inlineToolButton + '--small');

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
        const small = document.createElement(this.tag);

        small.classList.add(this.class);
        small.appendChild(selectedText);
        range.insertNode(small);

        this.api.selection.expandToTag(small);
    }

    unwrap(range) {
        const small = this.api.selection.findParentTag(this.tag, this.class);
        const text = range.extractContents();

        small.remove();

        range.insertNode(text);
    }

    checkState(selection) {
        const small = this.api.selection.findParentTag(this.tag);

        this.state = !!small;
    }

    static get sanitize() {
        return {
            small: {
                class: 'cdx-small'
            }
        };
    }
}
