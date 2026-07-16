<?php

namespace App\Actions\Permohonan;

/**
 * The outcome of a form/document completeness check, carrying indicators of the
 * missing fields and documents so they can be surfaced to the Pemohon.
 */
final class CompletenessResult
{
    /**
     * @param  array<int, string>  $missingFields
     * @param  array<int, string>  $missingDocuments
     */
    public function __construct(
        public array $missingFields = [],
        public array $missingDocuments = [],
    ) {}

    public function isComplete(): bool
    {
        return $this->missingFields === [] && $this->missingDocuments === [];
    }
}
