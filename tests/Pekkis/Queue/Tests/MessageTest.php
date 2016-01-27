<?php

namespace Pekkis\Queue\Tests;

use Pekkis\Queue\Message;

class MessageTest extends \Pekkis\Queue\Tests\TestCase
{

    /**
     * @test
     */
    public function initializesProperly()
    {
        $topic = 'test';
        $data = array('message' => 'All your base are belong to us');

        $message = Message::create($topic, $data);

        $this->assertEquals($data, $message->getData());
        $this->assertEquals($topic, $message->getTopic());
        $this->assertUuid($message->getUuid());

    }

    /**
     * @test
     */
    public function isRestorableFromArray()
    {
        $arr = array(
            'uuid' => 'lussutus-uuid',
            'topic' => 'lussutusviesti',
            'data' => array('lussutappa' => 'tussia')
        );

        $message = Message::fromArray($arr);

        $this->assertEquals($arr['data'], $message->getData());
        $this->assertEquals($arr['uuid'], $message->getUuid());
        $this->assertEquals($arr['topic'], $message->getTopic());
    }

    /**
     * @test
     */
    public function internalDataWorks()
    {
        $message = Message::create('luss', array('mussutus' => 'kovaa mussutusta'));

        $this->assertNull($message->getIdentifier());
        $this->assertSame($message, $message->setIdentifier('loso'));
        $this->assertEquals('loso', $message->getIdentifier());
    }

    /**
     * @test
     */
    public function setDataSetsData()
    {
        $message = Message::create('luss', array('mussutus' => 'kovaa mussutusta'));

        $message->setData('lussuttakeepa imaisua');

        $this->assertEquals('lussuttakeepa imaisua', $message->getData());
    }
}
