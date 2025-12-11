<?php
/**
 * GestiWork ERP - Capabilities
 *
 * This file is part of GestiWork ERP.
 *
 * GestiWork ERP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GestiWork ERP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GestiWork ERP. If not, see <https://www.gnu.org/licenses/>.
 */

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
