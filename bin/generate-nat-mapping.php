<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

require_once dirname(__DIR__).'/vendor/autoload.php';
$baseDir = dirname(__DIR__);

use LC\Portal\Config;
use LC\Portal\IP;

try {
    $ipFour = true;
    $ipSix = false;
    $externalIpFour = '192.0.2.1';
    $externalIpSix = '2001:db8::1';
    $portsPerHost = 256;
    $firstPort = 10000;
    $profileIdList = [];

    for ($i = 1; $i < $argc; ++$i) {
        if ('-4' === $argv[$i]) {
            $ipFour = true;
            $ipSix = false;
            continue;
        }
        if ('-6' === $argv[$i]) {
            $ipFour = false;
            $ipSix = true;
            continue;
        }
        if ('--external-v4' === $argv[$i]) {
            if ($i + 1 < $argc) {
                $externalIpFour = $argv[$i + 1];
            }
            continue;
        }
        if ('--external-v6' === $argv[$i]) {
            if ($i + 1 < $argc) {
                $externalIpSix = $argv[$i + 1];
            }
            continue;
        }
        if ('--profile' === $argv[$i]) {
            if ($i + 1 < $argc) {
                $profileIdList[] = $argv[$i + 1];
            }
            continue;
        }
        if ('--ports-per-host' === $argv[$i]) {
            if ($i + 1 < $argc) {
                $portsPerHost = (int) $argv[$i + 1];
            }
            continue;
        }
        if ('--first-port' === $argv[$i]) {
            if ($i + 1 < $argc) {
                $firstPort = (int) $argv[$i + 1];
            }
            continue;
        }
        if ('--help' === $argv[$i] || '-h' === $argv[$i]) {
            $appName = $argv[0];
            echo <<< EOF
                SYNTAX: $appName
                    [--profile ID]                  Only Profile ID (can be specified
                                                    multiple times)
                    [-4]                            Only IPv4
                    [-6]                            Only IPv6
                    [--ports-per-host N]            Ports per VPN client (256)
                    [--first-port N]                First port (10000)
                    [--external-v4 EXTERNAL_IP]     External IPv4 address
                    [--external-v6 EXTERNAL_IP]     External IPv6 address

                EOF;
            exit(0);
        }
    }

    $config = Config::fromFile($baseDir.'/config/config.php');

    if (0 === count($profileIdList)) {
        $profileConfigList = $config->profileConfigList();
    } else {
        $profileConfigList = [];
        foreach ($profileIdList as $profileId) {
            $profileConfigList[] = $config->profileConfig($profileId);
        }
    }

    if ($ipFour) {
        $clientIpCount = 0;
        echo '###############################################################################'.\PHP_EOL;
        echo '# IPv4                                                                        #'.\PHP_EOL;
        echo '###############################################################################'.\PHP_EOL;
        foreach ($profileConfigList as $profileConfig) {
            echo '# Profile: "'.$profileConfig->displayName().'" ('.$profileConfig->profileId().')'.\PHP_EOL;
            $ipFourRange = IP::fromIpPrefix($profileConfig->range());
            $splitCount = count($profileConfig->vpnProtoPorts());
            $ipFourSplitRangeList = $ipFourRange->split($splitCount);
            foreach ($ipFourSplitRangeList as $ipFourSplitRange) {
                $clientIpList = $ipFourSplitRange->clientIpList();
                foreach ($clientIpList as $clientIp) {
                    $minPort = 10000 + $clientIpCount * $portsPerHost;
                    $maxPort = 10000 + ($clientIpCount + 1) * $portsPerHost - 1;
                    echo '-A POSTROUTING -s '.$clientIp.' -p tcp -j SNAT --to-source '.$externalIpFour.':'.$minPort.'-'.$maxPort.\PHP_EOL;
                    echo '-A POSTROUTING -s '.$clientIp.' -p udp -j SNAT --to-source '.$externalIpFour.':'.$minPort.'-'.$maxPort.\PHP_EOL;
                    ++$clientIpCount;
                }
            }
        }
    }

    if ($ipSix) {
        $clientIpCount = 0;
        echo '###############################################################################'.\PHP_EOL;
        echo '# IPv6                                                                        #'.\PHP_EOL;
        echo '###############################################################################'.\PHP_EOL;
        foreach ($profileConfigList as $profileConfig) {
            echo '# Profile: "'.$profileConfig->displayName().'" ('.$profileConfig->profileId().')'.\PHP_EOL;
            $ipFourRange = IP::fromIpPrefix($profileConfig->range());
            $ipSixRange = IP::fromIpPrefix($profileConfig->range6());
            $splitCount = count($profileConfig->vpnProtoPorts());
            $ipFourSplitRangeList = $ipFourRange->split($splitCount);
            $ipSixSplitRangeList = $ipSixRange->split($splitCount);
            foreach ($ipSixSplitRangeList as $k => $ipSixSplitRange) {
                // we look at the IPv4 range size as that dictates how many
                // IPv6 IPs we need to match the number of IPv4 addresses
                $ipCount = count($ipFourSplitRangeList[$k]->clientIpList());
                $clientIpList = $ipSixSplitRange->clientIpList($ipCount);
                foreach ($clientIpList as $clientIp) {
                    $minPort = 10000 + $clientIpCount * $portsPerHost;
                    $maxPort = 10000 + ($clientIpCount + 1) * $portsPerHost - 1;
                    echo '-A POSTROUTING -s '.$clientIp.' -p tcp -j SNAT --to-source ['.$externalIpSix.']:'.$minPort.'-'.$maxPort.\PHP_EOL;
                    echo '-A POSTROUTING -s '.$clientIp.' -p udp -j SNAT --to-source ['.$externalIpSix.']:'.$minPort.'-'.$maxPort.\PHP_EOL;
                    ++$clientIpCount;
                }
            }
        }
    }
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage().\PHP_EOL;
    exit(1);
}
