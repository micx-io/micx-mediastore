<?php
namespace App;

use App\Type\RepoConf;
use App\Type\StorageFacet;
use Brace\Command\CommandModule;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Brace\Dbg\BraceDbg;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;
use Brace\Router\RouterModule;
use Brace\Router\Type\RouteParams;

use Lack\Freda\Filesystem\PosixFileSystem;
use Lack\Freda\FredaModule;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\ObjectStore\Driver\GoogleObjectStoreDriver;
use Phore\ObjectStore\ObjectStore;
use Phore\VCS\VcsFactory;
use Psr\Http\Message\ServerRequestInterface;


BraceDbg::SetupEnvironment(true, ["192.168.178.20", "localhost", "localhost:5000", "pagebuilder.leuffen.de"]);


AppLoader::extend(function () {
    $app = new BraceApp();

    // Use Laminas (ZendFramework) Request Handler
    $app->addModule(new BraceRequestLaminasModule());

    // Use the Uri-Based Routing
    $app->addModule(new RouterModule());
    $app->addModule(new CommandModule());

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

    $app->define("storageFacet", new DiService(function(ObjectStore $publicStore, ObjectStore $privateStore) {
        return new StorageFacet($publicStore, $privateStore, "default");
    }));

    // Define the app so it is also available in dependency-injection
    $app->define("app", new DiValue($app));


    return $app;
});
