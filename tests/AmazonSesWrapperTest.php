<?php

namespace Test;

use Aws\Credentials\Credentials;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Util\Uri;

require_once 'BaseWrapperTest.php';
require_once 'MockSender.php';

class AmazonSesWrapperTest extends BaseWrapperTest
{
    /**
     * @return \Test\MockSender
     */
    public function getMock($envelope)
    {
        $object = $this->getMockBuilder(AmazonSesWrapper::class)
            ->setMethods(['getSesClient'])
            ->setConstructorArgs([new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION')])
            ->getMock();

        $mock = new MockSender();
        $object->expects($this->once())
            ->method('getSesClient')
            ->will($this->returnValue($mock));

        $object->send($envelope);

        return $mock;
    }

    public function testGetSesClient()
    {
        $sesWrapper = new AmazonSesWrapper(new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION'));
        $sesClient = $sesWrapper->getSesClient();

        $credentials = $sesClient->getCredentials()->wait(true);
        $this->assertEquals(
            new Credentials(
                'ACCESS_KEY_ID',
                'SECRET_KEY'
            ),
            $credentials
        );
        $this->assertEquals('REGION', $sesClient->getRegion());
        $this->assertEquals('2010-12-01', $sesClient->getApi()->getApiVersion());
    }

    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();

        $mock = $this->getMock($envelope);
        $mimeMessage = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/basicenvelope.txt'));
        $mock->result['RawMessage']['Data'] = $this->fixVariableFields($mock->result['RawMessage']['Data']);

        $expected = [
            'RawMessage' => [
                'Data' => $mimeMessage
                ]
        ];

        $this->assertEquals($expected, $mock->result);
    }
}
