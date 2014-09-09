<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class Integration_OnlineInfo_OnlineLicenseCheckRequestFormationTest
 *
 * @covers oxOnlineServerEmailBuilder
 * @covers oxOnlineCaller
 * @covers oxSimpleXml
 * @covers oxOnlineLicenseCheckCaller
 * @covers oxUserCounter
 * @covers oxOnlineLicenseCheck
 */
class Integration_OnlineInfo_OnlineLicenseCheckRequestFormationTest extends OxidTestCase
{
    public function testRequestFormationWithExistingSerials()
    {
        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('aSerials', array('license_key'));
        $oConfig->setConfigParam('sClusterId', array('generated_unique_cluster_id'));
        $oConfig->setConfigParam('aServersData', array(
            array(
                'id' => 'server_id1',
                'timestamp' => '1409919510',
                'ip' => '127.0.0.1',
                'lastFrontendUsage' => '1409919510',
                'lastAdminUsage' => '1409919510',
        )));

        $sEdition = $oConfig->getEdition();
        $sVersion = $oConfig->getVersion();
        $sShopUrl = $oConfig->getShopUrl();
        $sRevision = $oConfig->getRevision();
        $iAdminUsers = 1;

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<olcRequest>';
        $sXml .=   '<pVersion>1.0</pVersion>';
        $sXml .=   '<keys><key>license_key</key></keys>';
        if ($sRevision) {
            $sXml .= "<revision>$sRevision</revision>";
        } else {
            $sXml .= '<revision/>';
        }
        $sXml .=   '<productSpecificInformation>';
        $sXml .=     '<servers>';
        $sXml .=       '<server>';
        $sXml .=         '<id>server_id1</id>';
        $sXml .=         '<timestamp>1409919510</timestamp>';
        $sXml .=         '<ip>127.0.0.1</ip>';
        $sXml .=         '<lastFrontendUsage>1409919510</lastFrontendUsage>';
        $sXml .=         '<lastAdminUsage>1409919510</lastAdminUsage>';
        $sXml .=       '</server>';
        $sXml .=     '</servers>';
        $sXml .=     '<counters>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>admin users</name>';
        $sXml .=         "<value>$iAdminUsers</value>";
        $sXml .=       '</counter>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>subShops</name>';
        $sXml .=         '<value>1</value>';
        $sXml .=       '</counter>';
        $sXml .=     '</counters>';
        $sXml .=   '</productSpecificInformation>';
        $sXml .=   '<clusterId>generated_unique_cluster_id</clusterId>';
        $sXml .=   "<edition>$sEdition</edition>";
        $sXml .=   "<version>$sVersion</version>";
        $sXml .=   "<shopUrl>$sShopUrl</shopUrl>";
        $sXml .=   '<productId>eShop</productId>';
        $sXml .= '</olcRequest>'."\n";

        $oCurl = $this->getMock('oxCurl', array('setParameters', 'execute'));
        $oCurl->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $sXml)));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = new oxOnlineServerEmailBuilder();
        $oOnlineCaller = new oxOnlineCaller($oCurl, $oEmailBuilder);

        $oSimpleXml = new oxSimpleXml();
        $oLicenseCaller = new oxOnlineLicenseCheckCaller($oOnlineCaller, $oSimpleXml);

        $oUserCounter = new oxUserCounter();
        $oLicenseCheck = new oxOnlineLicenseCheck($oLicenseCaller, $oUserCounter);

        $oLicenseCheck->validateShopSerials();
    }

    public function testRequestFormationWithNewSerial()
    {
        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('aSerials', array('license_key'));
        $oConfig->setConfigParam('sClusterId', array('generated_unique_cluster_id'));
        $oConfig->setConfigParam('aServersData', array(
            array(
                'id' => 'server_id1',
                'timestamp' => '1409919510',
                'ip' => '127.0.0.1',
                'lastFrontendUsage' => '1409919510',
                'lastAdminUsage' => '1409919510',
            )));

        $sEdition = $oConfig->getEdition();
        $sVersion = $oConfig->getVersion();
        $sShopUrl = $oConfig->getShopUrl();
        $sRevision = $oConfig->getRevision();
        $iAdminUsers = 1;

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<olcRequest>';
        $sXml .=   '<pVersion>1.0</pVersion>';
        $sXml .=   '<keys>';
        $sXml .=   '<key>license_key</key>';
        $sXml .=   '<key state="new">new_serial</key>';
        $sXml .=   '</keys>';
        if ($sRevision) {
            $sXml .= "<revision>$sRevision</revision>";
        } else {
            $sXml .= '<revision/>';
        }
        $sXml .=   '<productSpecificInformation>';
        $sXml .=     '<servers>';
        $sXml .=       '<server>';
        $sXml .=         '<id>server_id1</id>';
        $sXml .=         '<timestamp>1409919510</timestamp>';
        $sXml .=         '<ip>127.0.0.1</ip>';
        $sXml .=         '<lastFrontendUsage>1409919510</lastFrontendUsage>';
        $sXml .=         '<lastAdminUsage>1409919510</lastAdminUsage>';
        $sXml .=       '</server>';
        $sXml .=     '</servers>';
        $sXml .=     '<counters>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>admin users</name>';
        $sXml .=         "<value>$iAdminUsers</value>";
        $sXml .=       '</counter>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>subShops</name>';
        $sXml .=         '<value>1</value>';
        $sXml .=       '</counter>';
        $sXml .=     '</counters>';
        $sXml .=   '</productSpecificInformation>';
        $sXml .=   '<clusterId>generated_unique_cluster_id</clusterId>';
        $sXml .=   "<edition>$sEdition</edition>";
        $sXml .=   "<version>$sVersion</version>";
        $sXml .=   "<shopUrl>$sShopUrl</shopUrl>";
        $sXml .=   '<productId>eShop</productId>';
        $sXml .= '</olcRequest>'."\n";

        $oCurl = $this->getMock('oxCurl', array('setParameters', 'execute'));
        $oCurl->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $sXml)));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = new oxOnlineServerEmailBuilder();
        $oOnlineCaller = new oxOnlineCaller($oCurl, $oEmailBuilder);

        $oSimpleXml = new oxSimpleXml();
        $oLicenseCaller = new oxOnlineLicenseCheckCaller($oOnlineCaller, $oSimpleXml);

        $oUserCounter = new oxUserCounter();
        $oLicenseCheck = new oxOnlineLicenseCheck($oLicenseCaller, $oUserCounter);

        $oLicenseCheck->validateNewSerial('new_serial');
    }
}