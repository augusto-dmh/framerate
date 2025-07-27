<?php

namespace App\Providers;

use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use PHPUnit\Framework\ExpectationFailedException;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        AssertableInertia::macro('hasResource', function (string $key, JsonResource $resource): static {
            $props = $this->toArray()['props'];

            if (!isset($props[$key])) {
                throw new ExpectationFailedException("Property '{$key}' not found in view props");
            }

            // Handle paginated resources
            $viewData = $props[$key]['data'] ?? $props[$key];
            $expectedData = $this->normalizeData($resource->jsonSerialize());

            $hasMatch = collect($viewData)->contains(
                fn(array $item) => $this->normalizeData($item) === $expectedData
            );

            if (!$hasMatch) {
                throw new ExpectationFailedException(
                    "Resource not found in view prop '{$key}'"
                );
            }

            return $this;
        });

        AssertableInertia::macro('hasCollection', function (string $key, Collection $collection): static {
            $props = $this->toArray()['props'];

            if (!isset($props[$key])) {
                throw new ExpectationFailedException("Property '{$key}' not found in view props");
            }

            // Handle paginated collections
            $viewData = $props[$key]['data'] ?? $props[$key];

            if (!is_array($viewData)) {
                throw new ExpectationFailedException(
                    "View prop '{$key}' is not a collection/array"
                );
            }

            $expectedCount = $collection->count();
            $actualCount = count($viewData);

            if ($actualCount !== $expectedCount) {
                throw new ExpectationFailedException(
                    "Collection count mismatch for '{$key}': expected {$expectedCount}, got {$actualCount}"
                );
            }

            // Check if all collection items are present in view data
            // This is more flexible than exact matching
            $collectionIds = $collection->pluck('id')->toArray();
            $viewIds = collect($viewData)->pluck('id')->toArray();

            $missingIds = array_diff($collectionIds, $viewIds);

            if (!empty($missingIds)) {
                throw new ExpectationFailedException(
                    "Collection items with IDs [" . implode(', ', $missingIds) . "] not found in view prop '{$key}'"
                );
            }

            return $this;
        });

        AssertableInertia::macro('normalizeData', function (array $data): array {
            return collect($data)
                ->map(fn(mixed $value) => match (true) {
                    is_array($value) => $this->normalizeData($value),
                    default => $this->normalizeValue($value)
                })
                ->toArray();
        });

        AssertableInertia::macro('normalizeValue', function (mixed $value): mixed {
            return match (true) {
                // Date/time objects
                $value instanceof \DateTimeInterface =>
                    Carbon::parse($value)->toISOString(),

                // Stringable objects
                $value instanceof \Stringable =>
                    (string) $value,

                // Objects with __toString
                is_object($value) && method_exists($value, '__toString') =>
                    (string) $value,

                // Numeric strings
                is_string($value) && is_numeric($value) =>
                    str_contains($value, '.') ? (float) $value : (int) $value,

                // Date strings
                is_string($value) && $this->isDateString($value) =>
                    $this->parseAsDate($value),

                // Boolean-like strings
                is_string($value) =>
                    $this->parseBooleanString($value),

                // Default: return as-is
                default => $value
            };
        });

        AssertableInertia::macro('isDateString', function (string $value): bool {
            $patterns = [
                '/^\d{4}-\d{2}-\d{2}$/',                                    // YYYY-MM-DD
                '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',                  // YYYY-MM-DD HH:MM:SS
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/',                   // ISO 8601 (with optional timezone)
            ];

            return collect($patterns)->contains(
                fn(string $pattern) => preg_match($pattern, $value)
            );
        });

        AssertableInertia::macro('parseAsDate', function (string $value): string {
            try {
                return Carbon::parse($value)->toISOString();
            } catch (\Exception) {
                return $value; // Return original if parsing fails
            }
        });

        AssertableInertia::macro('parseBooleanString', function (string $value): mixed {
            $normalized = strtolower(trim($value));

            return match ($normalized) {
                'true', '1', 'yes', 'on' => true,
                'false', '0', 'no', 'off', '' => false,
                default => $value
            };
        });
    }
}
