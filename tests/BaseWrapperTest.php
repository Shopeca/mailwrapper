<?php

namespace Test;

use ByJG\Mail\Envelope;
use ByJG\Mail\Util;
use PHPUnit\Framework\TestCase;

abstract class BaseWrapperTest extends TestCase
{
    /**
     * @return \ByJG\Mail\Envelope
     */
    public function getBasicEnvelope()
    {
        $envelope = new Envelope(
            Util::getFullEmail('from@email.com', "João"),
            Util::getFullEmail('to@email.com', "John"),
            'Subject in 中国 and русский and português',
            '<h1>Title</h1>Body'
        );
        return $envelope;
    }

    public function getFullEnvelope()
    {
        $envelope = $this->getBasicEnvelope();
        $envelope->addTo('to2@email.com', 'Name');
        $envelope->addCC('cc1@email.com');
        $envelope->addCC('cc2@email.com');
        $envelope->addBCC('bcc1@email.com');
        $envelope->addBCC('bcc2@email.com');
        return $envelope;
    }

    public function getAttachmentEnvelope()
    {
        $envelope = $this->getFullEnvelope();
        $envelope->addAttachment('myname', __DIR__ . '/resources/attachment1.txt', 'text/plain');
        $envelope->addAttachment('myname2', __DIR__ . '/resources/attachment2.txt', 'text/plain');
        return $envelope;
    }

    public function getEmbedImageEnvelope()
    {
        $envelope = $this->getFullEnvelope();
        $envelope->addEmbedImage('myname', __DIR__ . '/resources/moon.png', 'image/png');
        $envelope->addEmbedImage('myname2', __DIR__ . '/resources/sun.png', 'image/png');
        $envelope->setBody('<h1>Title</h1>Body<img src="cid:myname"><img src="cid:myname2">');
        return $envelope;
    }

    protected function fixVariableFields($text)
    {
        $text = preg_replace(
            [
                '~\w+, \d+ \w+ \w+ \d+:\d+:\d+ [+-]\d+~',
                '~(--\w{2}_)\w+(--|\r)~',
                '~(="\w{2}_)\w+(")~',
                '~Message-ID: <[^@]+@[^>]+>~'
            ],
            [
                'xxx, dd, yyyy hh:mi:ss +ffff',
                '$1boundarydelimiter$2',
                '$1boundarydelimiter$2',
                'Message-ID: <boundarydelimiter@host>'
            ],
            $text
        );

        return $text;
    }
}
