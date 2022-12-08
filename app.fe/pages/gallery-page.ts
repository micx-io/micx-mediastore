import {customElement, ka_sleep, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";
import {ImageDetailsModal} from "../modals/image-details-modal";

// language=html
let html = `
        
<div class="container-xxl">
    <div class="row">
        <div class="col-2 m-0 p-1" ka.for="let curMedia of index.media">
            <div class="card m-0" ka.on.click="$fn.details(curMedia)">
                <div class="card-body position-relative p-1">
                    
                    <div class="bg-karo rounded">
                        <div class="ratio ratio-1x1 text-center " style="background-size: cover;background-repeat:no-repeat; background-position: center center" ka.style.background-image="'url(' + index.baseUrl + curMedia.previewUrl + ')'">
                        </div>
                    </div>
                    
                    <span class="position-absolute top-0 start-0 badge bg-secondary bg-opacity-50 m-1">[[curMedia.extension]]</span>
                    <span class="position-absolute top-0 end-0 badge bg-secondary bg-opacity-50 m-1">[[ (curMedia.width + "x" + curMedia.height) ]]</span>
                </div>
                
            </div>
            
        </div>
    </div>
    
</div>
<nav class="navbar position-fixed bottom-0 w-100 bg-light border-top">
    <div class="container">
        <app-file-upload></app-file-upload>
    </div>


</nav>


`

@customElement("gallery-page")
@route("gallery", "/static/{subscription_id}/{scope_id}")
@template(html)
class GalleryPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({

            $fn: {
                details: (media) => (new ImageDetailsModal()).show(scope.index, media)
            }
        })
    }

    async connectedCallback(): Promise<void> {
        super.connectedCallback();

        let subId = currentRoute.route_params["subscription_id"];
        let scopeId = currentRoute.route_params["scope_id"];
        this.scope.importFrom({
            index: await (await fetch("/v1/api/" + subId + "/" + scopeId + "/index.json")).json(),
        });
        console.log("json loaded");
        (new ImageDetailsModal()).show(this.scope.index, this.scope.index.media[0]);
        this.scope.render();
    }


    // language=html

}
