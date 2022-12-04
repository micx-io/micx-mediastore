import {KaCustomFragment, template} from "@kasimirjs/embed";


let html = `
<div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">
        <span>Original Url</span>
        <span class="badge bg-primary">[[ media.width + "x" + media.height ]]</span>
        <span class="badge bg-secondary">[[ parseInt(media.size / 1000) + " kB" ]]</span>
    </label>
    <input type="url" ka.attr.value="index.baseUrl + media.origUrl" readonly class="form-control" id="exampleFormControlInput1">
</div>
<div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">
        <span>Text</span>
        <span class="badge bg-primary">[[ media.width + "x" + media.height ]]</span>
        <span class="badge bg-secondary">[[ parseInt(media.size / 1000) + " kB" ]]</span>
    </label>
    <input type="url" ka.bind="$scope.wurst" class="form-control" id="exampleFormControlInput1">
</div>
<div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">
        <span>Original Url</span>
        <span class="badge bg-primary">[[ media.width + "x" + media.height ]]</span>
        <span class="badge bg-secondary">[[ parseInt(media.size / 1000) + " kB" ]]</span>
    </label>
    <select type="url" ka.options="media.variant.map(e => e.variantId)" ka.bind="$scope.selected" readonly class="form-select" id="exampleFormControlInput1"></select>
</div>
<div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">
        <span>Original Url</span>
        
    </label>
    <textarea type="url" ka.bind="$scope.code" readonly class="form-select" id="exampleFormControlInput1"></textarea>
</div>`


@template(html)
export class ImageDetailsOverviewFragment extends KaCustomFragment {

    constructor() {
        super();

        let scope = this.init({
            selected: null,
            code: "Bitte wählen",
            $on: {
                change: () => {
                    console.log(scope.dump());
                    let variant = scope.media.variant.filter(v => v.variantId === scope.selected)[0];
                    let code = variant.extensions.map(e => scope.index.baseUrl + "/" + variant.url + "." + e);
                    scope.code = code.join("\n");
                }
            }
        })
    }

}
