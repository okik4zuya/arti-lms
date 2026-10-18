<?php

namespace App\Services\Payments;

/** A failed call to Xendit (see PaymentGatewayException). */
class XenditException extends PaymentGatewayException {}
