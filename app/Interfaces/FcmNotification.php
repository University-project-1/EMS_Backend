<?php

namespace App\Interfaces;

interface FcmNotification
{
  public function toFcm($notifiable);
}
