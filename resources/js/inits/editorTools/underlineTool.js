export default class UnderlineTool {
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

    get shortcut() {
        return 'CTRL+U';
    }

    constructor({api}) {
        this.api = api;
        this.button = null;
        this._state = false;

        this.tag = 'U';
        this.class = 'cdx-underline';
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.innerHTML = '<i class="fas fa-underline"></i>';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.classList.add(this.api.styles.inlineToolButton + '--underline');

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
        const underline = document.createElement(this.tag);

        underline.classList.add(this.class);
        underline.appendChild(selectedText);
        range.insertNode(underline);

        this.api.selection.expandToTag(underline);
    }

    unwrap(range) {


        const underline = this.api.selection.findParentTag(this.tag, this.class);
        const text = range.extractContents();

        console.log(this);
        console.log(this.api);
        console.log(underline);
        console.log(text);
        console.log(range);

        underline.remove();

        range.insertNode(text);
    }

    checkState(selection) {
        const mark = this.api.selection.findParentTag(this.tag);

        this.state = !!mark;
    }

    static get sanitize() {
        return {
            u: {
                class: 'cdx-underline'
            }
        };
    }
}
