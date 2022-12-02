import {customElement, KaHtmlElement} from "@kasimirjs/embed";
import {currentRoute} from "@kasimirjs/app";
import {router} from "@kasimirjs/app";
import {UploadModal} from "./upload-modal";

@customElement("app-file-upload")
class FileUpload extends KaHtmlElement {


    doUpload() {

    }

    connected(): Promise<void> {
        let scope = {
            progress: null as string
        }

        document.addEventListener("paste", async (e : ClipboardEvent) => {
            console.log(JSON.stringify(e.clipboardData.files[0]));
            let data = await (new UploadModal()).show();

            console.log("paste", e);
            let formData = new FormData();
            formData.append("file", e.clipboardData.files[0]);
        });

        this.$tpl.render(scope);
        scope.$ref.upload1.addEventListener("change", async ()=> {
            let files = scope.$ref.upload1.files;
            console.log(files);
            scope.progress = "0%";
            this.$tpl.render();
            for (let index=0; index < files.length; index++) {
                let file = files[index];
                let formData = new FormData();
                console.log(file);
                formData.append("file", file);
                scope.progress = ((index / files.length) * 100) + "%"
                this.$tpl.render();
                await fetch(`/v1/api/${router.currentRoute.route_params['subscription_id']}/${router.currentRoute.route_params['scope_id']}/upload`, {
                    method: "POST",
                    body: formData
                });

            }
            scope.progress = null;
            this.$tpl.render();
            scope.$ref.upload1.value = "";
        })
        return null
    }

    disconnected(): Promise<void> {
        return Promise.resolve(undefined);
    }

    // language=html
    html = `
<div>
    <input ka.ref="'upload1'" type="file" multiple>
    <div class="progress" ka.if="progress !== null">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" ka.style.width="progress"></div>
    </div>
</div>`
}
