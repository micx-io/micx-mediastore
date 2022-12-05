import {KaCustomFragment, template} from "@kasimirjs/embed";
import {KitFormInput} from "@kasimirjs/kit-bootstrap";


let html = `
<div class="mb-3" ka.for="let form of forms">
    <ka-use ka.use="form"></ka-use>
</div>

<div class="mb-3">
    <h2>Bild auswählen</h2>
    <div class="input-group mb-3">
        <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroupSelect01">Größe:</label>
            <select class="form-select" value="include" ka.bind="$scope.selectedVariant" ka.options="media.variant.map((e) => e.variantId)" id="inputGroupSelect01">
            </select>
            
            <label class="input-group-text" for="inputGroupSelect01">Format:</label>
            <label ka.if="selectedVariant === null"class="input-group-text" for="inputGroupSelect01"></label>
            <select ka.if="selectedVariant !== null" class="form-select" value="include" ka.bind="$scope.selectedExtension" ka.options="media.variant.find((e) => e.variantId === selectedVariant).extensions" id="inputGroupSelect01">
            </select>
        </div>
        
    </div>

    <div ka.if="selectedImage !== null && selectedExtension !== null">
        <label for="exampleFormControlInput1" class="form-label">
            <span>Quell-Url:</span>
            <span class="badge bg-primary">[[ selectedImage.width + "x" + selectedImage.height ]]</span>
            <span class="badge bg-secondary">[[ parseInt(selectedImage.size / 1000) + " kB" ]]</span>
        </label>
        <input type="url" ka.bind="$scope.selectedUrl" readonly class="form-control mb-3">
        
        <div class="ratio-1x1 ratio border-primary bg-karo d-flex w-50">
            <a ka.attr.href="$scope.selectedUrl" class="w-100 h-100 d-flex" target="_blank"><img class="h-auto h-auto my-auto mx-auto" style="max-width: 100%; max-height: 100%" ka.attr.src="$scope.selectedUrl"></a>
        </div>
    </div>
    
       
</div>
`;


@template(html)
export class ImageDetailsOverviewFragment extends KaCustomFragment {

    constructor() {
        super();

        let scope = this.init({
            selectedVariant: null,
            selectedExtension: null,
            selectedImage: null,
            selectUrl:null,
            forms: [
                new KitFormInput("$scope.media.name", {title: "Dateiname", readonly: true}),
                new KitFormInput("$scope.media.license", {title: "Lizenz", readonly: true})
            ],
            code: "Bitte wählen",
            $on: {
                change: () => {
                    console.log(scope.dump());
                    let variant = scope.media.variant.filter(v => v.variantId === scope.selectedVariant)[0];
                    scope.selectedImage = variant;
                    let code = variant.extensions.map(e => scope.index.baseUrl + "/" + variant.url + "." + e);
                    scope.code = code.join("\n");
                    scope.selectedUrl = scope.index.baseUrl + variant.url + "." + scope.selectedExtension;
                }
            }
        })
    }

}
