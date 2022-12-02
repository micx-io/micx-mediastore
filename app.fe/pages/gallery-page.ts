import {customElement, KaHtmlElement} from "@kasimirjs/embed";
import {route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";



@customElement("gallery-page")
@route("gallery", "/static/{subscription_id}/{scope_id}")
class GalleryPage extends KaHtmlElement {

    constructor(public route : CurRoute) {
        super();

    }

    async connected(): Promise<void> {

        let subId = currentRoute.route_params["subscription_id"];
        let scopeId = currentRoute.route_params["scope_id"];
        let scope = {
            index: await (await fetch("/v1/api/" + subId + "/" + scopeId + "/index.json")).json()
        }
        this.$tpl.render(scope);
    }

    async disconnected() : Promise<void> {

    }


    // language=html
    html = `
        
       <div class="container-xxl">
        <div class="row">
            <div class="col-2 m-0 p-1" ka.for="let curMedia of index.media">
                <div class="card m-0">
                    <div class="card-body position-relative p-1">
                        
                        <div class="ratio ratio-1x1 text-center" style="background-size: cover;background-repeat:no-repeat; background-position: center center" ka.style.background-image="'url(' + index.baseUrl + curMedia.previewUrl + ')'">
                        </div>
                        <span class="position-absolute top-0 start-0 badge bg-secondary bg-opacity-50 m-1">[[curMedia.extension]]</span>
                        <span class="position-absolute top-0 end-0 badge bg-secondary bg-opacity-50 m-1">[[ (curMedia.width + "x" + curMedia.height) ]]</span>
                    </div>
                    
                </div>
                
            </div>

        </div>
        </div>
    `
}
