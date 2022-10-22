

class DropHelper {

    static CurPlacer = null;

    /**
     *
     * @param selector {HTMLElement}
     * @param placer
     */
    constructor(placer=KaToolsV1.html`<div style='height:0;position: relative;padding:0;margin: 0'><div style="position:absolute;height:2px;background-color: #0a58ca;width:100%;margin-top:-2px"></div></div>`) {
        this._placer = placer.content.firstElementChild;

    }

    _findBeforeElement(selector, target) {
        do {
            if (target === null)
                return null;
            if (target === selector)
                return null;
            if (target.parentElement  === selector)
                return target;
            target = target.parentElement;
        } while (true)
    }


    /**
     *
     * @param selector {HTMLElement}
     *
     */
    bind(selector, ondrop=null, onSort=null) {
        /**
         *
         * @type {HTMLElement}
         */
        let curBeforeElement;
        selector.addEventListener("dragover", async (e)=> {
            e.preventDefault();
            if (e.target === null)
                return;
            await KaToolsV1.debounce(50,100);
            let beforeElement = this._findBeforeElement(selector, e.target);
            if (curBeforeElement === beforeElement)
                return
            curBeforeElement = beforeElement;

            if (beforeElement === DropHelper.CurPlacer && DropHelper.CurPlacer !== null)
                return;

            if (DropHelper.CurPlacer !== null)
                DropHelper.CurPlacer.remove();
            DropHelper.CurPlacer = this._placer.cloneNode(true);

            selector.insertBefore(DropHelper.CurPlacer, beforeElement);
        });

        selector.addEventListener("dragleave", (e)=>  {
            if (e.target !== selector)
                return;
            if (DropHelper.CurPlacer !== null)
                DropHelper.CurPlacer.remove();
            DropHelper.CurPlacer = null;
        })



        selector.addEventListener("drop", (e)=> {
            e.preventDefault();
            if (DropHelper.CurPlacer !== null)
                DropHelper.CurPlacer.remove();

            let dropData = JSON.parse(e.dataTransfer.getData("text/json"));

            console.log("drop", dropData.dropid, DropHelper._find_dropid(selector))
            if (dropData.dropid === DropHelper._find_dropid(selector)) {
                if (onSort !== null) {
                    let targetIndex = null; // append
                    if (curBeforeElement !== null)
                        targetIndex = curBeforeElement._ka_for_index
                    onSort(dropData.index, targetIndex, dropData.data);
                }
            } else {
                if (ondrop !== null) {
                    let index = null;
                    if (curBeforeElement !== null) {
                        index = curBeforeElement._ka_for_index;
                    }
                    ondrop(e, index, dropData.data);
                }
            }
        });
    }


}


DropHelper._find_dropid = function (element) {
    do {
        if (element.hasAttribute("dropid")) {
            return element.getAttribute("dropid");
        }
        element = element.parentElement;
        if (element === null)
            return null;
    } while (true)
}

/**
 *
 * @param element {HTMLElement}
 * @param data
 */
DropHelper.make_dragable = function(element, data) {
    element.draggable = true;
    element.addEventListener("dragstart", (e)=> {
        element.firstElementChild.classList.add("bg-primary");
        e.dataTransfer.setData("text/json", JSON.stringify({
            dropid: DropHelper._find_dropid(element),
            index: element._ka_for_index,
            data
        }));
    });

}
