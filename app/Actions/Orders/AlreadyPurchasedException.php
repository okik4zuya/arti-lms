<?php

namespace App\Actions\Orders;

use RuntimeException;

/** The email already has an active license: a second purchase would only be a duplicate payment. */
class AlreadyPurchasedException extends RuntimeException {}
