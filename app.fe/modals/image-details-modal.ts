
import {ImageDetailsOverviewFragment} from "./image-details-modal/fragments/overview-tab";
import {createScopeObject, KaModal, template} from "@kasimirjs/embed";
import {KitTabPane} from "@kasimirjs/kit-bootstrap";
import {KaCustomModal} from "@kasimirjs/embed";

let html = `
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
                        <ka-use ka.use="tabPane"></ka-use>
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

@template(html)
export class ImageDetailsModal extends KaCustomModal {


    public show(index, media): Promise<null> {
        let scope = this.init({
            index,
            media,
            tabPane: new KitTabPane({
                "Übersicht": new ImageDetailsOverviewFragment(),
                "Daten": new ImageDetailsOverviewFragment()
            }),
            $fn: {
                close: ()=>this.resolve(null)
            },

        });
        return super.show();
    }


    // language=html


}
