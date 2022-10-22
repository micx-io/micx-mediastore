<?php

define("DEV_MODE", (bool)"1");
define("CONF_SSH_KEY_FILE", "/opt/ssh_keys");

define("STANDALONE", (bool)"0");
define("STANDALONE_PATH", "/opt/mock/repo");

const CONF_API_MOUNT = "/v1/api";

const DEFAULT_IS_CHANGED_FILE = "/.git/pb_chanded";

//if (DEV_MODE === true) {
    define("CONF_REPO_PATH", "/opt/mock/repo");

    /*
} else {
    define("CONF_REPO_PATH", "/data");
}
    */


