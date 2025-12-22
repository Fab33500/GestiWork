<?php
/**
 * GestiWork ERP - Helper d'autorisation
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

namespace GestiWork\Infrastructure\Security;

final class GwAuth
{
    /**
     * @param array<string, mixed> $context
     */
    public static function can(string $permission, array $context = []): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        return current_user_can('manage_options');
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function enforce(string $permission, array $context = [], int $statusCode = 403): void
    {
        if (self::can($permission, $context)) {
            return;
        }

        wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), $statusCode);
    }
}
