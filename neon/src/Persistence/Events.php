<?php
namespace Neon\Persistence;

use Neon\Domain\Event\Event;

interface Events
{
    /**
     * @return Event[]
     */
    public function fetchEvents(): array;
    public function testDb();
    public function writeEvents();
}
