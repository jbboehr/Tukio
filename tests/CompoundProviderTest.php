<?php
declare(strict_types=1);

namespace Crell\Tukio;


use PHPUnit\Framework\TestCase;
use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\ListenerProviderInterface;

class CompoundProviderTest extends TestCase
{
    public function test_multiple_providers() : void
    {
        $provider1 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(EventInterface $event): iterable
            {
                yield function (CollectingEvent $event) { $event->add('C'); };
                yield function (CollectingEvent $event) { $event->add('R'); };
            }
        };
        $provider2 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(EventInterface $event): iterable
            {
                yield function (CollectingEvent $event) { $event->add('E'); };
                yield function (CollectingEvent $event) { $event->add('L'); };
                yield function (CollectingEvent $event) { $event->add('L'); };
            }
        };


        $p = new CompoundProvider();

        $p->addProvider($provider1)
          ->addProvider($provider2);

        $event = new CollectingEvent();

        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        $this->assertEquals('CRELL', implode($event->result()));
    }
}
