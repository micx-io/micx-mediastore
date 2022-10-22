<?php

namespace App;


use App\Helper\FileServeMw;
use App\Type\RepoConf;
use Brace\Auth\Basic\AuthBasicMiddleware;
use Brace\Auth\Basic\BasicAuthToken;
use Brace\Auth\Basic\Validator\LambdaAuthValidator;
use Brace\Body\BodyMiddleware;
use Brace\Core\AppLoader;
use Brace\Core\Base\ExceptionHandlerMiddleware;
use Brace\Core\Base\JsonReturnFormatter;
use Brace\Core\Base\NotFoundMiddleware;
use Brace\Core\BraceApp;
use Brace\Router\RouterDispatchMiddleware;
use Brace\Router\RouterEvalMiddleware;
use Brace\SpaServe\Loaders\EsbuildLoader;
use Brace\SpaServe\SpaStaticFileServerMw;


AppLoader::extend(function (BraceApp $app) {

    $app->setPipe([
        // We want to access the Body via $body dependency
        new BodyMiddleware(),

        // We want to provide nice json formatted Errors
        new ExceptionHandlerMiddleware(),

        // Lets evaluate the Uri for Routes
        new RouterEvalMiddleware(),

        // This is our dynamic Frontend. Everything below /static is handled here
        new SpaStaticFileServerMw(
            __DIR__ . "/../www/spaserve",
            liveReload: DEV_MODE,
            loaders: [
                new EsbuildLoader("/app.js", "app.fe/index.ts", "text/javascript", "/opt", false, false)
            ]

        ),

        // From this Line: Only API Requests

        // Do Authentication via Basic Auth
        new AuthBasicMiddleware(new LambdaAuthValidator(function(BasicAuthToken $basicAuthToken, RepoConf $repoConf) {
            if (STANDALONE === true) {
                return true;
            }
            foreach ($repoConf->auth as $curAuth) {
                [$user, $hash] = explode(":", $curAuth);
                if ($basicAuthToken->user === $user && password_verify($basicAuthToken->passwd, $hash))
                    return true;
            }
            return false;
        })),

        // Dispatch the Route by the Controllers defined in 20_api_routes.php
        // and format object returns with JSON
        new RouterDispatchMiddleware([
            new JsonReturnFormatter($app)
        ]),

        // Return a 404 error if RouterDispatch couldn't dispatch route
        new NotFoundMiddleware()
    ]);
});


