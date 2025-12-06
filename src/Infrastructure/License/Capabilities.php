<?php

declare(strict_types=1);

namespace GestiWork\Infrastructure\License;

/**
 * Capabilities centralise les capacités fonctionnelles disponibles
 * selon que le plugin tourne en mode Core ou Pro.
 *
 * Pour l'instant, tout est en mode Core (Pro = false par défaut).
 */
final class Capabilities
{
    private bool $proActive;

    private function __construct(bool $proActive)
    {
        $this->proActive = $proActive;
    }

    /**
     * Fabrique une instance pour le mode Core (sans licence Pro active).
     */
    public static function core(): self
    {
        return new self(false);
    }

    /**
     * Indique si le mode Pro est actif (licence de services valide).
     */
    public function isPro(): bool
    {
        return $this->proActive;
    }

    /**
     * Exemples de capacités fonctionnelles (toutes false en Core pour l'instant).
     */
    public function canExportCompta(): bool
    {
        return $this->proActive;
    }

    public function supportsMultiOrganisme(): bool
    {
        return $this->proActive;
    }

    public function hasAdvancedSubcontractorFeatures(): bool
    {
        return $this->proActive;
    }
}
