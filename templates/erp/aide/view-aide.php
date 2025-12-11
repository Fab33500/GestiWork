<?php
/**
 * GestiWork ERP - Vue Aide
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

$gw_help_section = get_query_var('gw_section');
if ($gw_help_section === '' && isset($_GET['gw_section'])) {
    $gw_help_section = (string) $_GET['gw_section'];
}
if (!in_array($gw_help_section, ['introduction', 'demarrage', 'configuration', 'options', 'quotidien', 'faq'], true)) {
    $gw_help_section = '';
}

?>
<section class="gw-section" data-gw-help-section="<?php echo esc_attr($gw_help_section); ?>">
    <h2 class="gw-section-title"><?php esc_html_e('Aide GestiWork ERP', 'gestiwork'); ?></h2>
    <p class="gw-section-description">
        <?php esc_html_e('Cette section regroupe la documentation d\'utilisation de GestiWork ERP pour un organisme de formation ou un independant.', 'gestiwork'); ?>
    </p>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Sommaire', 'gestiwork'); ?></h3>
        <ul class="gw-list">
            <li><a href="#gw-aide-introduction"><?php esc_html_e('Présentation générale', 'gestiwork'); ?></a></li>
            <li><a href="#gw-aide-demarrage"><?php esc_html_e('Prise en main et navigation', 'gestiwork'); ?></a></li>
            <?php if (current_user_can('manage_options')) : ?>
                <li><a href="#gw-aide-configuration"><?php esc_html_e('Configuration et paramétrage', 'gestiwork'); ?></a></li>
                <li><a href="#gw-aide-options"><?php esc_html_e('Onglet Options', 'gestiwork'); ?></a></li>
            <?php endif; ?>
            <li><a href="#gw-aide-quotidien"><?php esc_html_e('Utilisation au quotidien', 'gestiwork'); ?></a></li>
            <li><a href="#gw-aide-faq"><?php esc_html_e('Questions fréquentes', 'gestiwork'); ?></a></li>
        </ul>
    </div>

    <?php require __DIR__ . '/sections/section-introduction.php'; ?>
    <?php require __DIR__ . '/sections/section-demarrage.php'; ?>
    <?php require __DIR__ . '/sections/section-configuration.php'; ?>
    <?php require __DIR__ . '/sections/section-quotidien.php'; ?>
    <?php require __DIR__ . '/sections/section-faq.php'; ?>
</section>

<script>
    (function () {
        var container = document.querySelector('.gw-section[data-gw-help-section]');
        var initialSectionSlug = container ? container.getAttribute('data-gw-help-section') : '';
        var aideSections = document.querySelectorAll('.gw-aide-section');
        if (!aideSections.length) {
            return;
        }

        function showSectionById(sectionId) {
            var found = false;
            aideSections.forEach(function (section) {
                if (section.id === sectionId) {
                    section.style.display = '';
                    found = true;
                } else {
                    section.style.display = 'none';
                }
            });
            return found;
        }

        // Masquer toutes les sections au chargement
        aideSections.forEach(function (section) {
            section.style.display = 'none';
        });

        if (initialSectionSlug) {
            var initialIdFromSection = 'gw-aide-' + initialSectionSlug;
            if (showSectionById(initialIdFromSection)) {
                return;
            }
        }

        // Gestion du clic sur le sommaire
        var summaryLinks = document.querySelectorAll('.gw-section .gw-list a[href^="#gw-aide-"]');
        summaryLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var target = link.getAttribute('href');
                if (!target || target.charAt(0) !== '#') {
                    return;
                }

                e.preventDefault();
                var sectionId = target.substring(1);
                if (showSectionById(sectionId)) {
                    if (typeof window.history.replaceState === 'function') {
                        window.history.replaceState(null, '', target);
                    } else {
                        window.location.hash = target;
                    }

                    var sectionEl = document.getElementById(sectionId);
                    if (sectionEl && typeof sectionEl.scrollIntoView === 'function') {
                        sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // Si une ancre est présente dans l'URL au chargement, on affiche directement la section correspondante
        var initialHash = window.location.hash;
        if (initialHash && initialHash.charAt(0) === '#') {
            var initialId = initialHash.substring(1);
            showSectionById(initialId);
        }
    })();
</script>
