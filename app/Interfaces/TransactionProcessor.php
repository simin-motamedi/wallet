<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface TransactionProcessor
{
    public function validate(Model $model): void;

    public function process(Model $model, int $otp);

}
