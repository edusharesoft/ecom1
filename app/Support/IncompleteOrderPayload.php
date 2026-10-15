<?php

namespace App\Support;

/**
 * ইনকমপ্লিট অর্ডার — নতুন চেকআউট (লোকেশন + কার্ট JSON) ও পুরনো ফ্ল্যাট items ফরম্যাট।
 */
class IncompleteOrderPayload
{
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            $digits = '0' . substr($digits, 3);
        }

        return $digits;
    }

    /**
     * @param  mixed  $items
     * @return array<int, array<string, mixed>>
     */
    public static function lineItems($items): array
    {
        if (! is_array($items)) {
            if (is_string($items) && $items !== '') {
                $decoded = json_decode($items, true);

                return self::lineItems(is_array($decoded) ? $decoded : []);
            }

            return [];
        }

        if (isset($items['line_items']) && is_array($items['line_items'])) {
            return array_values(array_filter($items['line_items'], [self::class, 'isProductRow']));
        }

        $rows = [];
        foreach ($items as $key => $row) {
            if ($key === '_checkout_meta' || $key === 'meta') {
                continue;
            }
            if (self::isProductRow($row)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @param  mixed  $items
     * @return array<string, mixed>
     */
    public static function meta($items): array
    {
        if (! is_array($items)) {
            return [];
        }

        if (isset($items['meta']) && is_array($items['meta'])) {
            return $items['meta'];
        }

        if (isset($items['_checkout_meta']) && is_array($items['_checkout_meta'])) {
            return $items['_checkout_meta'];
        }

        return [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lineItems
     * @param  array<string, mixed>  $meta
     * @return array{line_items: array, meta: array}
     */
    public static function pack(array $lineItems, array $meta = []): array
    {
        return [
            'line_items' => array_values($lineItems),
            'meta'       => $meta,
        ];
    }

    public static function isProductRow(mixed $row): bool
    {
        return is_array($row) && (isset($row['id']) || isset($row['name']));
    }

    public static function subtotalFromLineItems(array $lineItems): float
    {
        $sum = 0.0;
        foreach ($lineItems as $item) {
            $qty   = (int) ($item['qty'] ?? 1);
            $price = (float) ($item['price'] ?? 0);
            $sum += $qty * $price;
        }

        return $sum;
    }
}
