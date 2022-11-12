import {customElement, KaHtmlElement} from "@kasimirjs/embed";
import {route, router} from "@kasimirjs/app";

@customElement("gallery-page")
@route("gallery", "/static/gallery")
class GalleryPage extends KaHtmlElement {

    async connected(): Promise<void> {
        let scope = {
            index: await (await fetch("/v1/api/index.json")).json()
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
                    <div class="card-body p-1">
                        <div class="ratio ratio-1x1 text-center" style="background-size: cover" ka.style.background-image="'url(https://cdn.leuffen.de/default/' + curMedia.previewUrl + ')'">
                        </div>
                    </div>
                    
                </div>
                
            </div>

        </div>
        </div>
    `
}
