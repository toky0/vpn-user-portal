<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\Jwt\Keys\EdDSA\SecretKey;
use LC\Portal\FileIO;

$baseDir = dirname(__DIR__);
$apiKeyFile = $baseDir.'/config/oauth.key';
$nodeKeyFile = $baseDir.'/config/node.key';

try {
    // OAuth key
    if (!FileIO::exists($apiKeyFile)) {
        $secretKey = SecretKey::generate();
        FileIO::writeFile($apiKeyFile, $secretKey->encode(), 0644);
    }

    // Node Key
    if (!FileIO::exists($nodeKeyFile)) {
        $secretKey = random_bytes(32);
        FileIO::writeFile($nodeKeyFile, sodium_bin2hex($secretKey), 0644);
    }
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage().\PHP_EOL;
    exit(1);
}