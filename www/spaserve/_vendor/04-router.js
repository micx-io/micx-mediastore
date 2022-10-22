KaToolsV1.routes = {};
KaToolsV1.route = {
    "name": null,
    "route_orig": null,
    "params": {},
    "search": new URLSearchParams(window.location.search)
}

function ka_href(route_name=null, params = {}, search = null) {
    if (route_name === null)
        route_name = KaToolsV1.route.name;

    params = {...KaToolsV1.route.params, ...params};
    let route = KaToolsV1.routes[route_name];

    route = route.replace(/{([a-zA-Z0-9_]+)}/g, (match, name) => {
        return params[name];
    });
    if (search !== null)
        return route + "?" + (new URLSearchParams(search));
    return route;
}

function ka_goto(route_name, params={}, search=null) {

    window.location.href = ka_href(route_name, params, search);
}

function ka_import_node(e) {
    if (e instanceof HTMLTemplateElement) {
        return document.importNode(e.content, true).cloneNode(true);
    }
    return e.cloneNode(true);
}


class KasimirV1_Router extends HTMLElement {


    routeDef2RegEx(route) {
        // console.log(route);
        let regex = new RegExp(/{(?<param>[a-zA-Z0-9_]+)}/g)
        let r = route.replace(regex, (matches, param) => {
            return `(?<${param}>[^/]+)`
        });
        return `^${r}$`;
    }

    async connectedCallback() {
        this.style.display = "contents"; // Important: To render correctly in row
        await KaToolsV1.domReady();
        let pathname = location.pathname;
        if (pathname.endsWith("/"))
            pathname = pathname.slice(0, -1);

        let foundRouteElement = null;
        let defaultElement = null;
        for (let e of this.children) {
            //console.log(e);
            if (e.hasAttribute("default_route")) {
                defaultElement = e;
                continue;
            }
            if (e.getAttribute("route") !== null) {
                let route = e.getAttribute("route");
                if (route.endsWith("/"))
                    route = route.slice(0, -1);

                KaToolsV1.routes[e.getAttribute("route_name")] = route;

                let regex = new RegExp(this.routeDef2RegEx(route));

                let match = regex.exec(pathname);
                if (match === null)
                    continue;

                KaToolsV1.route.name = e.getAttribute("route_name");
                KaToolsV1.route.params = match.groups;
                if (typeof KaToolsV1.route.params === "undefined")
                    KaToolsV1.route.params = {}
                //console.log("route routing", KaToolsV1.route);
                foundRouteElement = e;

            }
        }
        if (foundRouteElement !== null)
            this.append(ka_import_node(foundRouteElement));
        else
            console.error("[ka-router] Undefined route: " + pathname);
            //this.append(ka_import_node(defaultElement));
    }

}

customElements.define("ka-router", KasimirV1_Router);
