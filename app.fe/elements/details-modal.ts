import {KaModal} from "@kasimirjs/embed";


export class DetailsModal extends KaModal {


    public show(index, media): Promise<null> {
        let scope = {
            index,
            media,
            selected: null,
            code: "Bitte wählen",
            $fn: {
                close: ()=>this.resolve(null)
            },
            $on: {
                change: () => {
                    let variant = scope.media.variant.filter(v => v.variantId === scope.selected)[0];
                    let code = variant.extensions.map(e => index.baseUrl + "/" + variant.url + "." + e);
                    scope.code = code.join("\n");
                    this.render();
                }
            }
        }
        this.render(scope);

        return super.show();
    }


    // language=html
    html = `
        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><b>Bild Details:</b> [[ media.name + "." + media.extension ]]</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ka.on.click="$fn.close()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="ratio-1x1 ratio border-primary bg-karo">
                                    <a ka.attr.href="index.baseUrl + media.origUrl" target="_blank"><img class="h-auto w-100" ka.attr.src="index.baseUrl + media.previewUrl"></a>
                                </div>
                               
                            </div>
                            
                            <div class="col-lg-9">

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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" ka.on.click="$fn.close()">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        
    `;

}
