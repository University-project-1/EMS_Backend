<?php

namespace App\Interfaces;

interface FcmNotification
{
    /**
     * @return array{
     *     notification: array{title: string, body: string},
     *     data: array<string, string>
     * }
     */
    public function toFcm(object $notifiable): array;
}
