<?php

return [
  'methods' => [
    'card' => 'カード支払い',
    'konbini' => 'コンビ二払い',
  ],

  'method_types' => [
    'card'    => ['card'],
    'konbini' => ['konbini'],
  ],

  'fake' => env('PAYMENTS_FAKE', false),

];