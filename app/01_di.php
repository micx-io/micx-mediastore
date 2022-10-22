<?php
namespace App;

use App\Type\RepoConf;
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
use Phore\VCS\VcsFactory;
use Psr\Http\Message\ServerRequestInterface;


BraceDbg::SetupEnvironment(true, ["192.168.178.20", "localhost", "localhost:5000", "pagebuilder.leuffen.de"]);


AppLoader::extend(function () {
    $app = new BraceApp();

    // Use Laminas (ZendFramework) Request Handler
    $app->addModule(new BraceRequestLaminasModule());

    // Use the Uri-Based Routing
    $app->addModule(new RouterModule());

    $app->addModule(new FredaModule(new PosixFileSystem(CONF_REPO_PATH, true)));

    // The git Repository
    $app->define("vcsFactory", new DiService(function () {
        $repo = new VcsFactory();
        $repo->setAuthSshPrivateKey(phore_file(CONF_SSH_KEY_FILE)->assertReadable()->get_contents());
        $repo->setCommitUser("woodwing", "woodwing@a-f.de");
        return $repo;
    }));

    // Where do we find the files / repo?
    $app->define("repoConf", new DiService(function () {
        if (STANDALONE === true) {
            $repoConf = new RepoConf(STANDALONE_PATH);
            return $repoConf;
        }
        return new RepoConf(CONF_REPO_PATH);
    }));

    // Define the app so it is also available in dependency-injection
    $app->define("app", new DiValue($app));


    return $app;
});
