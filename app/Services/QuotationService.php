<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function createQuotation(array $data, int $userId): Quotation
    {
        return DB::transaction(function () use ($data, $userId) {
            $quotation = new Quotation();
            $quotation->service_id = $data['service_id'];
            $quotation->quotation_title = $data['quotation_title'];
            $quotation->total_amount = $data['total_amount'];
            $quotation->currency = $data['currency'];
            $quotation->added_by = $userId;
            $quotation->save();

            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = [
                    'particular' => $item['name'],
                    'amount' => $item['amount'],
                    'quotation_id' => $quotation->id
                ];
            }
            QuotationItem::insert($items);

            return $quotation;
        });
    }

    public function updateQuotation(string $uniqueId, array $data): ?Quotation
    {
        return DB::transaction(function () use ($uniqueId, $data) {
            $quotation = Quotation::where('unique_id', $uniqueId)->first();
            if (!$quotation) {
                return null;
            }
            $quotation->service_id = $data['service_id'];
            $quotation->quotation_title = $data['quotation_title'];
            $quotation->total_amount = $data['total_amount'];
            $quotation->currency = $data['currency'];
            $quotation->save();

            QuotationItem::where('quotation_id', $quotation->id)->delete();

            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = [
                    'particular' => $item['name'],
                    'amount' => $item['amount'],
                    'quotation_id' => $quotation->id
                ];
            }
            QuotationItem::insert($items);

            return $quotation;
        });
    }

    public function deleteQuotation(string $uniqueId): bool
    {
        $quotation = Quotation::where('unique_id', $uniqueId)->first();
        if (!$quotation) {
            return false;
        }
        Quotation::deleteRecord($quotation->id);
        return true;
    }
} 