import {customElement, ka_sleep, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {messageBus, route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";
import {ImageDetailsModal} from "../modals/image-details-modal";

import {IndexUpdatedMessage} from "../messages/index-updated-message";

// language=html
let html = `
        
<div class="container-xxl">
    <div class="row" ka.if="index !== null">
        <div class="col-2 m-0 p-1" ka.for="let curMedia of index.media">
            <div class="card m-0"">
                <div class="card-body position-relative p-1">
                    
                    <div class="bg-karo rounded">
                        <div class="ratio ratio-1x1 text-center " style="background-size: cover;background-repeat:no-repeat; background-position: center center">
                            <img ka.attr.src="index.baseUrl + curMedia.previewUrl">
                        </div>
                    </div>
                    
                    <span class="position-absolute top-0 start-0 badge bg-primary bg-opacity-50 m-1">[[curMedia.type]]</span>
                    <span class="position-absolute top-0 end-0 badge bg-secondary bg-opacity-50 m-1">[[ (curMedia.width + "x" + curMedia.height) ]]</span>
                    
                    <div class="position-absolute badge bottom-0 end-0 badge bg-secondary bg-opacity-50 m-1">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void()" ka.on.click="$fn.details(curMedia)">Details</a></li>
                            </ul>
                        </div>
                    </div>
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
            index: null,
            $fn: {
                details: (media) => (new ImageDetailsModal()).show(scope.index, media)
            }
        })
    }

    async connectedCallback(): Promise<void> {
        super.connectedCallback();

        let subId = currentRoute.route_params["subscription_id"];
        let scopeId = currentRoute.route_params["scope_id"];

        messageBus.on(IndexUpdatedMessage, async () => {
            this.scope.importFrom({
                index: await (await fetch("/v1/api/" + subId + "/" + scopeId + "/index.json")).json(),
            });
            this.scope.render();
        })

        messageBus.trigger(new IndexUpdatedMessage())

    }


    // language=html

}
