<?php

namespace SuperVMar\Notification\Domain;

interface WebPushSender
{
    public function send(PushSubscriptions $subscriptions, array $payload): void;
}
