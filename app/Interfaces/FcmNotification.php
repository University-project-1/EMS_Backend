<?php

namespace App\Interfaces;

interface FcmNotification
{
  public function toFcm(object $notifiable);
}
