<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Interfaces\TransactionProcessor;
use App\Models\Invoice;
use App\Utils\Response;
use Illuminate\Http\JsonResponse;

class InvoicePrepareController extends Controller
{
    /**
     * @param TransactionProcessor $transaction
     */
    public function __construct(private TransactionProcessor $transaction)
    {
    }

    /**
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function __invoke(Invoice $invoice): JsonResponse
    {
        $this->transaction->validate($invoice);
        return Response::success([
            'message' => 'OTP sent successfully.',
        ]);
    }
}
