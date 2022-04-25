<?php

namespace App\Services;

use Carbon\Carbon;

class PresaleService
{
    const NOT_STARTED_TYPE = 0;
    const PRESALE_ONE_TYPE = 1;
    const PRESALE_TWO_TYPE = 2;
    const PRESALE_THREE_TYPE = 3;
    const CLAIM_TYPE = 99;

    /**
     * Settings.
     *
     * @var array
     */
    protected $settings;

    /**
     * Get presale type.
     *
     * @return int
     */
    public function getPresaleType(): int
    {
        $type = self::NOT_STARTED_TYPE;
        $fallbackDate = Carbon::now()->addCentury()->timestamp;
        $currentDate = Carbon::now()->timestamp;
        $settings = SettingsService::get();
        if ($currentDate > $settings['presale_one_start'] ?? $fallbackDate) {
            $type = self::PRESALE_ONE_TYPE;
        }
        if ($currentDate > $settings['presale_two_start'] ?? $fallbackDate) {
            $type = self::PRESALE_TWO_TYPE;
        }
        if ($currentDate > $settings['presale_three_start'] ?? $fallbackDate) {
            $type = self::PRESALE_THREE_TYPE;
        }
        if ($currentDate > $settings['claim_start'] ?? $fallbackDate) {
            $type = self::CLAIM_TYPE;
        }

        return $type;
    }

    /**
     * Get presale max.
     *
     * @return int
     */
    public function getPresaleMax(): int
    {
        $max = 0;
        $settings = $this->getSettings();
        switch ($this->getPresaleType()) {
            case self::NOT_STARTED_TYPE:
                $max = 0;
                break;
            case self::PRESALE_ONE_TYPE:
                $max = $settings['presale_one_max'] ?? 0;
                break;
            case self::PRESALE_TWO_TYPE:
                $max = $settings['presale_two_max'] ?? 0;
                break;
            case self::PRESALE_THREE_TYPE:
                $max = $settings['presale_three_max'] ?? 0;
                break;
            case self::CLAIM_TYPE:
                $max = 0;
                break;
        }

        return $max;
    }

    /**
     * Get presale price.
     *
     * @return int
     */
    public function getPresalePrice(): int
    {
        $price = 0;
        $settings = $this->getSettings();
        switch ($this->getPresaleType()) {
            case self::NOT_STARTED_TYPE:
                $price = 0;
                break;
            case self::PRESALE_ONE_TYPE:
                $price = $settings['presale_one_price'] ?? 0;
                break;
            case self::PRESALE_TWO_TYPE:
                $price = $settings['presale_two_price'] ?? 0;
                break;
            case self::PRESALE_THREE_TYPE:
                $price = $settings['presale_three_price'] ?? 0;
                break;
            case self::CLAIM_TYPE:
                $price = 0;
                break;
        }

        return $price;
    }

    /**
     * Get presale value.
     *
     * @return int
     */
    public function getPresaleValue(): int
    {
        $value = 0;
        $settings = $this->getSettings();
        switch ($this->getPresaleType()) {
            case self::NOT_STARTED_TYPE:
                $value = 0;
                break;
            case self::PRESALE_ONE_TYPE:
                $value = $settings['presale_one_value'] ?? 0;
                break;
            case self::PRESALE_TWO_TYPE:
                $value = $settings['presale_two_value'] ?? 0;
                break;
            case self::PRESALE_THREE_TYPE:
                $value = $settings['presale_three_value'] ?? 0;
                break;
            case self::CLAIM_TYPE:
                $value = 0;
                break;
        }

        return $value;
    }

    /**
     * Get presale salt.
     *
     * @return string|null
     */
    public function getPresaleSalt(): ?string
    {
        if (
            $this->getPresaleType() == self::NOT_STARTED_TYPE ||
            $this->getPresaleType() == self::CLAIM_TYPE
        ) {
            return null;
        }

        return implode('', [
            'max', $this->getPresaleMax(),
            'price', $this->getPresalePrice(),
            'value', $this->getPresaleValue(),
        ]);
    }

    /**
     * Get settings.
     *
     * @return array
     */
    protected function getSettings(): array
    {
        if (!$this->settings) {
            $this->settings = SettingsService::get();
        }

        return $this->settings;
    }
}
