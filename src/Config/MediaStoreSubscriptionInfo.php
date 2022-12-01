<?php

namespace App\Config;

class MediaStoreSubscriptionInfo
{
   /**
     * @var string[]
     */
    public array $scopes = [];

    /**
     * @var string[]
     */
    public array $auth = [];


    public function getScopeAccess(string $scope) : ?string {
        foreach ($this->scopes as $curScope) {
            if (strpos(":", $curScope) === -1)
                continue;
            [$curScopeName, $access] = explode(":", $curScope);
            if ($curScopeName === $scope)
                return $access;

        }
        return null;
    }

}
