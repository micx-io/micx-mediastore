

class TextAreaHelper {

    constructor(element, maxrows) {
        element.rows = element.value.split(/\n|\r|\r\n/g).length + 1
        element.addEventListener("keydown", function(e) {
            element.rows = this.value.split(/\n|\r|\r\n/g).length + 1
            if (e.key !== 'Tab')
                return;
            e.preventDefault();
            var start = element.selectionStart;
            var end = element.selectionEnd;

            // set textarea value to: text before caret + tab + text after caret
            element.value = element.value.substring(0, start) +
                "\t" + element.value.substring(end);

            // put caret at right position again
            element.selectionStart =
                element.selectionEnd = start + 1;

        });

    }

}
