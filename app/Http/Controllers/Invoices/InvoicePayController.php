<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Requests\InvoicePayRequest;
use App\Interfaces\TransactionProcessor;
use App\Models\Invoice;
use App\Utils\Response;
use Illuminate\Http\JsonResponse;

class InvoicePayController
{
    /**
     * @param TransactionProcessor $transaction
     */
    public function __construct(private TransactionProcessor $transaction)
    {
    }

    /**
     * @param InvoicePayRequest $request
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function __invoke(InvoicePayRequest $request, Invoice $invoice): JsonResponse
    {
        $this->transaction->process($invoice, $request->otp);
        return Response::success($invoice->toArray());
    }
}
