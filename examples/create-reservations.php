<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Queue;
use DateTime;
use Pekkis\Queue\Message;

require_once __DIR__ . '/real-life-bootstrap.php';

$numberOfReservations = (isset($argv[1])) ? $argv[1] : 100;

/** @var Queue $queue */

// Empties the queue
$queue->purge();

for ($x = 1; $x <= $numberOfReservations; $x = $x + 1) {

    $now = time();
    $from = rand($now, $now + 10000);
    $to = rand($now + 10001, $now + 20000);

    $reservationRequest = new ReservationRequest(
        DateTime::createFromFormat('U', $from),
        DateTime::createFromFormat('U', $to)
    );

    $queue->enqueue('reservation.create', $reservationRequest);
}
