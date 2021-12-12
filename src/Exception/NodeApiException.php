<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace Vpn\Portal\Exception;

use Exception;

class NodeApiException extends Exception
{
    /** @var null|string */
    private $userId;

    /**
     * @param null|string $userId
     * @param string      $message
     * @param int         $code
     */
    public function __construct($userId, $message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->userId = $userId;
    }

    /**
     * @return null|string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
