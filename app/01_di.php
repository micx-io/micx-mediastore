<?php
namespace App;

use App\Business\processors\DownloadStorageProcessor;
use App\Business\processors\ImageStorageProcessor;
use App\Business\processors\PdfStorageProcessor;
use App\Business\processors\SvgStorageProcessor;
use App\Business\StorageFacet;
use App\Config\MediaStoreConf;
use App\Config\MediaStoreSubscriptionInfo;
use Brace\Command\CommandModule;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Brace\Dbg\BraceDbg;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;
use Brace\Router\RouterModule;
use Brace\Router\Type\RouteParams;
use Lack\Subscription\Brace\SubscriptionClientModule;
use Lack\Subscription\Type\T_Subscription;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\ObjectStore\Driver\GoogleObjectStoreDriver;
use Phore\ObjectStore\ObjectStore;


BraceDbg::SetupEnvironment(true, ["192.168.178.20", "localhost", "localhost:5000", "mediastore.leuffen.de"]);


AppLoader::extend(function () {
    $app = new BraceApp();

    // Use Laminas (ZendFramework) Request Handler
    $app->addModule(new BraceRequestLaminasModule());

    // Use the Uri-Based Routing
    $app->addModule(new RouterModule());
    $app->addModule(new CommandModule());

    $app->addModule(
        new SubscriptionClientModule(
            CONF_SUBSCRIPTION_ENDPOINT,
            CONF_SUBSCRIPTION_CLIENT_ID,
            CONF_SUBSCRIPTION_CLIENT_SECRET
        )
    );

    // The git Repository
    $app->define("publicStore", new DiService(function () {
        $objectStore = new ObjectStore(new GoogleObjectStoreDriver(
            CONF_GCLOUD_INDENTY_FILE,
            CONF_GCLOUD_BUCKET ,
            ["predefinedAcl" => "publicRead"])
        );
        return $objectStore;
    }));

    $app->define("privateStore", new DiService(function () {
        $objectStore = new ObjectStore(new GoogleObjectStoreDriver(
            CONF_GCLOUD_INDENTY_FILE,
            CONF_GCLOUD_BUCKET ,
            ["predefinedAcl" => "projectprivate"])
        );
        return $objectStore;
    }));

    $app->define("storageFacet", new DiService(function(ObjectStore $publicStore, ObjectStore $privateStore, MediaStoreConf $mediaStoreConf) {
        $facet =  new StorageFacet($publicStore, $privateStore, $mediaStoreConf->scope);
        $facet->addProcessor(new ImageStorageProcessor());
        $facet->addProcessor(new SvgStorageProcessor());
        $facet->addProcessor(new PdfStorageProcessor());
        $facet->addProcessor(new DownloadStorageProcessor());
        return $facet;
    }));

    $app->define("mediaStoreConf", new DiService(function (T_Subscription $subscription, RouteParams $routeParams) {

        $subscriptionId = $routeParams->get("subscription_id");

        $subInfo = $subscription->getClientPrivateConfig(null, MediaStoreSubscriptionInfo::class);

        $scopeId = "";
        $access = "none";
        if ($routeParams->has("scope_id")) {
            $scopeId = $routeParams->get("scope_id");
            $access = $subInfo->getScopeAccess($scopeId);
            if ($access === null)
                throw new \Exception("Scope access to scope '$scopeId' denied:");
        }


        return new MediaStoreConf(
            $scopeId,
            $subscriptionId,
            $access,
            $subInfo
        );
    }));


    // Define the app so it is also available in dependency-injection
    $app->define("app", new DiValue($app));


    return $app;
});
