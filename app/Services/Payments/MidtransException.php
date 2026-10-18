<?php

namespace App\Services\Payments;

/** A failed call to Midtrans (see PaymentGatewayException). */
class MidtransException extends PaymentGatewayException {}
