<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Portal\Tests;

use LC\Portal\IP;
use PHPUnit\Framework\TestCase;

class IPTest extends TestCase
{
    public function testIPv4One(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/24');
        $splitRange = $ip->split(1);
        $this->assertSame(1, \count($splitRange));
        $this->assertSame('192.168.1.0/24', (string) $splitRange[0]);
    }

    public function testNetmask(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/24');
        $this->assertSame('255.255.255.0', $ip->netmask());
        $ip = IP::fromIpPrefix('10.0.0.0/8');
        $this->assertSame('255.0.0.0', $ip->netmask());
        $ip = IP::fromIpPrefix('10.0.0.128/25');
        $this->assertSame('255.255.255.128', $ip->netmask());
        // it makes no sense for IPv6, but still fun :-P
        $ip = IP::fromIpPrefix('fd00::/64');
        $this->assertSame('ffff:ffff:ffff:ffff::', $ip->netmask());
    }

    public function testIPv4Two(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/24');
        $splitRange = $ip->split(2);
        $this->assertSame(2, \count($splitRange));
        $this->assertSame('192.168.1.0/25', (string) $splitRange[0]);
        $this->assertSame('192.168.1.128/25', (string) $splitRange[1]);

        $ip = IP::fromIpPrefix('0.0.0.0/0');
        $splitRange = $ip->split(2);
        $this->assertSame(2, \count($splitRange));
        $this->assertSame('0.0.0.0/1', (string) $splitRange[0]);
        $this->assertSame('128.0.0.0/1', (string) $splitRange[1]);
    }

    public function testIPv4Four(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/24');
        $splitRange = $ip->split(4);
        $this->assertSame(4, \count($splitRange));
        $this->assertSame('192.168.1.0/26', (string) $splitRange[0]);
        $this->assertSame('192.168.1.64/26', (string) $splitRange[1]);
        $this->assertSame('192.168.1.128/26', (string) $splitRange[2]);
        $this->assertSame('192.168.1.192/26', (string) $splitRange[3]);
    }

    public function testIPv4ThirtyTwo(): void
    {
        $ip = IP::fromIpPrefix('10.0.0.0/8');
        $splitRange = $ip->split(32);
        $this->assertSame(32, \count($splitRange));
        $this->assertSame('10.0.0.0/13', (string) $splitRange[0]);
        $this->assertSame('10.8.0.0/13', (string) $splitRange[1]);
        $this->assertSame('10.16.0.0/13', (string) $splitRange[2]);
        $this->assertSame('10.24.0.0/13', (string) $splitRange[3]);
        $this->assertSame('10.32.0.0/13', (string) $splitRange[4]);
        $this->assertSame('10.40.0.0/13', (string) $splitRange[5]);
        $this->assertSame('10.48.0.0/13', (string) $splitRange[6]);
        $this->assertSame('10.56.0.0/13', (string) $splitRange[7]);
        $this->assertSame('10.64.0.0/13', (string) $splitRange[8]);
        $this->assertSame('10.72.0.0/13', (string) $splitRange[9]);
        $this->assertSame('10.80.0.0/13', (string) $splitRange[10]);
        $this->assertSame('10.88.0.0/13', (string) $splitRange[11]);
        $this->assertSame('10.96.0.0/13', (string) $splitRange[12]);
        $this->assertSame('10.104.0.0/13', (string) $splitRange[13]);
        $this->assertSame('10.112.0.0/13', (string) $splitRange[14]);
        $this->assertSame('10.120.0.0/13', (string) $splitRange[15]);
        $this->assertSame('10.128.0.0/13', (string) $splitRange[16]);
        $this->assertSame('10.136.0.0/13', (string) $splitRange[17]);
        $this->assertSame('10.144.0.0/13', (string) $splitRange[18]);
        $this->assertSame('10.152.0.0/13', (string) $splitRange[19]);
        $this->assertSame('10.160.0.0/13', (string) $splitRange[20]);
        $this->assertSame('10.168.0.0/13', (string) $splitRange[21]);
        $this->assertSame('10.176.0.0/13', (string) $splitRange[22]);
        $this->assertSame('10.184.0.0/13', (string) $splitRange[23]);
        $this->assertSame('10.192.0.0/13', (string) $splitRange[24]);
        $this->assertSame('10.200.0.0/13', (string) $splitRange[25]);
        $this->assertSame('10.208.0.0/13', (string) $splitRange[26]);
        $this->assertSame('10.216.0.0/13', (string) $splitRange[27]);
        $this->assertSame('10.224.0.0/13', (string) $splitRange[28]);
        $this->assertSame('10.232.0.0/13', (string) $splitRange[29]);
        $this->assertSame('10.240.0.0/13', (string) $splitRange[30]);
        $this->assertSame('10.248.0.0/13', (string) $splitRange[31]);
    }

    public function testIPv6One(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/64');
        $splitRange = $ip->split(1);
        $this->assertSame(1, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
    }

    public function testIPv6OneWithMinSpace(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/112');
        $splitRange = $ip->split(1);
        $this->assertSame(1, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
    }

    public function testIPv6Two(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/64');
        $splitRange = $ip->split(2);
        $this->assertSame(2, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
        $this->assertSame('1111:2222:3333:4444::1:0/112', (string) $splitRange[1]);
    }

    public function testIPv6Four(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/64');
        $splitRange = $ip->split(4);
        $this->assertSame(4, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
        $this->assertSame('1111:2222:3333:4444::1:0/112', (string) $splitRange[1]);
        $this->assertSame('1111:2222:3333:4444::2:0/112', (string) $splitRange[2]);
        $this->assertSame('1111:2222:3333:4444::3:0/112', (string) $splitRange[3]);
    }

    public function testIPv6ThirtyTwo(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/64');
        $splitRange = $ip->split(32);
        $this->assertSame(32, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
        $this->assertSame('1111:2222:3333:4444::1:0/112', (string) $splitRange[1]);
        $this->assertSame('1111:2222:3333:4444::2:0/112', (string) $splitRange[2]);
        $this->assertSame('1111:2222:3333:4444::3:0/112', (string) $splitRange[3]);
        $this->assertSame('1111:2222:3333:4444::4:0/112', (string) $splitRange[4]);
        $this->assertSame('1111:2222:3333:4444::5:0/112', (string) $splitRange[5]);
        $this->assertSame('1111:2222:3333:4444::6:0/112', (string) $splitRange[6]);
        $this->assertSame('1111:2222:3333:4444::7:0/112', (string) $splitRange[7]);
        $this->assertSame('1111:2222:3333:4444::8:0/112', (string) $splitRange[8]);
        $this->assertSame('1111:2222:3333:4444::9:0/112', (string) $splitRange[9]);
        $this->assertSame('1111:2222:3333:4444::a:0/112', (string) $splitRange[10]);
        $this->assertSame('1111:2222:3333:4444::b:0/112', (string) $splitRange[11]);
        $this->assertSame('1111:2222:3333:4444::c:0/112', (string) $splitRange[12]);
        $this->assertSame('1111:2222:3333:4444::d:0/112', (string) $splitRange[13]);
        $this->assertSame('1111:2222:3333:4444::e:0/112', (string) $splitRange[14]);
        $this->assertSame('1111:2222:3333:4444::f:0/112', (string) $splitRange[15]);
        $this->assertSame('1111:2222:3333:4444::10:0/112', (string) $splitRange[16]);
        $this->assertSame('1111:2222:3333:4444::11:0/112', (string) $splitRange[17]);
        $this->assertSame('1111:2222:3333:4444::12:0/112', (string) $splitRange[18]);
        $this->assertSame('1111:2222:3333:4444::13:0/112', (string) $splitRange[19]);
        $this->assertSame('1111:2222:3333:4444::14:0/112', (string) $splitRange[20]);
        $this->assertSame('1111:2222:3333:4444::15:0/112', (string) $splitRange[21]);
        $this->assertSame('1111:2222:3333:4444::16:0/112', (string) $splitRange[22]);
        $this->assertSame('1111:2222:3333:4444::17:0/112', (string) $splitRange[23]);
        $this->assertSame('1111:2222:3333:4444::18:0/112', (string) $splitRange[24]);
        $this->assertSame('1111:2222:3333:4444::19:0/112', (string) $splitRange[25]);
        $this->assertSame('1111:2222:3333:4444::1a:0/112', (string) $splitRange[26]);
        $this->assertSame('1111:2222:3333:4444::1b:0/112', (string) $splitRange[27]);
        $this->assertSame('1111:2222:3333:4444::1c:0/112', (string) $splitRange[28]);
        $this->assertSame('1111:2222:3333:4444::1d:0/112', (string) $splitRange[29]);
        $this->assertSame('1111:2222:3333:4444::1e:0/112', (string) $splitRange[30]);
        $this->assertSame('1111:2222:3333:4444::1f:0/112', (string) $splitRange[31]);
    }

    public function testGetFirstHost(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/24');
        $splitRange = $ip->split(4);
        $this->assertSame(4, \count($splitRange));
        $this->assertSame('192.168.1.0/26', (string) $splitRange[0]);
        $this->assertSame('192.168.1.1', $splitRange[0]->firstHost());
        $this->assertSame('192.168.1.64/26', (string) $splitRange[1]);
        $this->assertSame('192.168.1.65', $splitRange[1]->firstHost());
        $this->assertSame('192.168.1.128/26', (string) $splitRange[2]);
        $this->assertSame('192.168.1.129', $splitRange[2]->firstHost());
        $this->assertSame('192.168.1.192/26', (string) $splitRange[3]);
        $this->assertSame('192.168.1.193', $splitRange[3]->firstHost());

        $ip = IP::fromIpPrefix('192.168.1.5/24');
        $this->assertSame('192.168.1.1', $ip->firstHost());
    }

    public function testGetFirstHost6(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::/64');
        $splitRange = $ip->split(4);
        $this->assertSame(4, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
        $this->assertSame('1111:2222:3333:4444::1', $splitRange[0]->firstHost());
        $this->assertSame('1111:2222:3333:4444::1:0/112', (string) $splitRange[1]);
        $this->assertSame('1111:2222:3333:4444::1:1', $splitRange[1]->firstHost());
        $this->assertSame('1111:2222:3333:4444::2:0/112', (string) $splitRange[2]);
        $this->assertSame('1111:2222:3333:4444::2:1', $splitRange[2]->firstHost());
        $this->assertSame('1111:2222:3333:4444::3:0/112', (string) $splitRange[3]);
        $this->assertSame('1111:2222:3333:4444::3:1', $splitRange[3]->firstHost());

        $ip = IP::fromIpPrefix('1111:2222:3333:4444::5/64');
        $this->assertSame('1111:2222:3333:4444::1', $ip->firstHost());
    }

    public function testIPv4NonFirstTwo(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.128/24');
        $splitRange = $ip->split(2);
        $this->assertSame(2, \count($splitRange));
        $this->assertSame('192.168.1.0/25', (string) $splitRange[0]);
        $this->assertSame('192.168.1.128/25', (string) $splitRange[1]);
    }

    public function testIPv6NonFirstTwo(): void
    {
        $ip = IP::fromIpPrefix('1111:2222:3333:4444::ffff/64');
        $splitRange = $ip->split(2);
        $this->assertSame(2, \count($splitRange));
        $this->assertSame('1111:2222:3333:4444::/112', (string) $splitRange[0]);
        $this->assertSame('1111:2222:3333:4444::1:0/112', (string) $splitRange[1]);
    }

    public function testHostIpListFour(): void
    {
        $ip = IP::fromIpPrefix('192.168.1.0/29');
        $hostIpList = $ip->clientIpList();
        $this->assertSame(5, \count($hostIpList));
        $this->assertSame(
            [
                '192.168.1.2',
                '192.168.1.3',
                '192.168.1.4',
                '192.168.1.5',
                '192.168.1.6',
            ],
            $hostIpList
        );
    }

    public function testHostIpListSix(): void
    {
        $ip = IP::fromIpPrefix('fd42::/64');
        $hostIpList = $ip->clientIpList(16);
        $this->assertSame(16, \count($hostIpList));
        $this->assertSame(
            [
                'fd42::1000',
                'fd42::1001',
                'fd42::1002',
                'fd42::1003',
                'fd42::1004',
                'fd42::1005',
                'fd42::1006',
                'fd42::1007',
                'fd42::1008',
                'fd42::1009',
                'fd42::100a',
                'fd42::100b',
                'fd42::100c',
                'fd42::100d',
                'fd42::100e',
                'fd42::100f',
            ],
            $hostIpList
        );
    }
}
