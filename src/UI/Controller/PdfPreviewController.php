<?php
/**
 * GestiWork ERP - PDF Preview Controller
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

namespace GestiWork\UI\Controller;

use GestiWork\Domain\Settings\SettingsProvider;
use GestiWork\Infrastructure\Database\ShortcodeSeeder;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Contrôleur pour la génération d'aperçu PDF.
 */
final class PdfPreviewController
{
    /**
     * Génère et affiche un aperçu PDF basé sur un template.
     *
     * @param int $templateId ID du template PDF
     */
    public static function renderPreview(int $templateId): void
    {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé.', 'gestiwork'), 403);
        }

        global $wpdb;

        $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

        // Récupérer le template
        $template = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tablePdfTemplates} WHERE id = %d LIMIT 1",
                $templateId
            ),
            ARRAY_A
        );

        if (!$template) {
            wp_die(__('Modèle PDF introuvable.', 'gestiwork'), 404);
        }

        // Récupérer les données de l'organisme pour les shortcodes
        $ofIdentity = SettingsProvider::getOfIdentity();

        // Construire le HTML du PDF
        $html = self::buildPdfHtml($template, $ofIdentity);

        // Générer le PDF avec Dompdf
        self::generatePdf($html, $template);
    }

    /**
     * Construit le HTML complet du PDF.
     *
     * @param array<string, mixed> $template
     * @param array<string, mixed> $ofIdentity
     * @return string
     */
    private static function buildPdfHtml(array $template, array $ofIdentity): string
    {
        $rawHeaderHtml = isset($template['header_html']) ? (string) $template['header_html'] : '';

        // Découper le contenu brut de l'en-tête en 3 zones optionnelles.
        // Si aucun marqueur [ZONE1]/[ZONE2]/[ZONE3] n'est présent, tout le
        // contenu reste en zone 1 (comportement identique à l'existant).
        [$headerRawZone1, $headerRawZone2, $headerRawZone3] = self::splitHeaderZones($rawHeaderHtml);

        $headerHtmlZone1 = self::replaceShortcodes($headerRawZone1, $ofIdentity);
        $headerHtmlZone2 = self::replaceShortcodes($headerRawZone2, $ofIdentity);
        $headerHtmlZone3 = self::replaceShortcodes($headerRawZone3, $ofIdentity);

        $footerHtml = self::replaceShortcodes($template['footer_html'] ?? '', $ofIdentity);

        $fontTitle = $template['font_title'] ?? 'sans-serif';
        $fontBody = $template['font_body'] ?? 'sans-serif';
        $fontTitleSize = (int) ($template['font_title_size'] ?? 14);
        $fontBodySize = (int) ($template['font_body_size'] ?? 11);
        $colorTitle = $template['color_title'] ?? '#000000';
        $colorOtherTitles = $template['color_other_titles'] ?? '#000000';
        $headerBgColor = $template['header_bg_color'] ?? 'transparent';
        $footerBgColor = $template['footer_bg_color'] ?? 'transparent';
        $customCss = $template['custom_css'] ?? '';

        $marginTop = (float) ($template['margin_top'] ?? 5);
        $marginBottom = (float) ($template['margin_bottom'] ?? 5);
        $marginLeft = (float) ($template['margin_left'] ?? 10);
        $marginRight = (float) ($template['margin_right'] ?? 10);

        // Hauteurs fixes (en mm) pour les zones réservées à l'en-tête et au pied de page.
        // Ces valeurs ne sont plus estimées automatiquement :
        // l'utilisateur les règle dans le modèle en fonction de son contenu.
        $headerHeight = (float) ($template['header_height'] ?? 20);
        $footerHeight = (float) ($template['footer_height'] ?? 15);

        // Marges effectives de la page en tenant compte des zones header/footer.
        // Le contenu principal (corps) s'écoule automatiquement entre ces marges.
        $topMarginWithHeader = $marginTop + $headerHeight;
        $bottomMarginWithFooter = $marginBottom + $footerHeight;

        // Contenu de démonstration
        $bodyContent = self::getDemoContent($ofIdentity);

        // Tailles de police calculées pour les sous-titres
        $h2Size = max(10, $fontTitleSize - 2);
        $h3Size = max(9, $fontTitleSize - 4);

        // URL vers la feuille de style PDF générique du plugin
        $pdfCssUrl = defined('GW_PLUGIN_URL')
            ? GW_PLUGIN_URL . 'assets/css/gw-pdf.css'
            : '';

        $cssLinkTag = $pdfCssUrl !== ''
            ? '<link rel="stylesheet" type="text/css" href="' . esc_url($pdfCssUrl) . '" />'
            : '';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aperçu PDF - {$template['name']}</title>
    {$cssLinkTag}
    <style>
        /* Marges de page : on inclut ici la marge "papier" + les bandes
           réservées à l'en-tête et au pied. Le corps du texte restera
           strictement entre ces marges. */
        @page {
            margin: {$topMarginWithHeader}mm {$marginRight}mm {$bottomMarginWithFooter}mm {$marginLeft}mm;
        }

        body {
            font-family: {$fontBody};
            font-size: {$fontBodySize}pt;
            line-height: 1.4;
            color: #333;
            margin: 35px 0 15px 0;
            padding: 10px;
        }

        h1, h2, h3 {
            font-family: {$fontTitle};
        }

        h1 {
            color: {$colorTitle};
            font-size: {$fontTitleSize}pt;
            margin-bottom: 10mm;
        }

        h2, h3, h4, h5, h6 {
            color: {$colorOtherTitles};
        }

        h2 {
            font-size: {$h2Size}pt;
            margin-top: 8mm;
            margin-bottom: 4mm;
        }

        h3 {
            font-size: {$h3Size}pt;
            margin-top: 6mm;
            margin-bottom: 3mm;
        }

        /* Bordures de debug pour bien visualiser les trois zones.
           L'en-tête et le pied sont positionnés DANS les marges de page
           (offset négatif) pour que le corps ne puisse pas les chevaucher).
           La hauteur définie dans 3.2 (header_height / footer_height) est
           une hauteur stricte : tout ce qui dépasse est coupé. */

        .pdf-header {
            position: fixed;
            /* Placé dans la bande de marge haute :
               - @page.margin-top = marge papier + headerHeight
               - top = -headerHeight remonte l'en-tête dans cette bande */
            top: -{$headerHeight}mm;
            left: -{$marginLeft}mm;
            right: -{$marginRight}mm;
            height: {$headerHeight}mm;
            background: {$headerBgColor};
            padding: 3mm {$marginLeft}mm;
        }

        .pdf-footer {
            position: fixed;
            /* Placé dans la bande de marge basse :
               - @page.margin-bottom = marge papier + footerHeight
               - bottom = -footerHeight descend le pied dans cette bande */
            bottom: -{$footerHeight}mm;
            left: -{$marginLeft}mm;
            right: -{$marginRight}mm;
            height: {$footerHeight}mm;
            background: {$footerBgColor};
            font-size: 9pt;
            color: #666;
            padding: 3mm {$marginLeft}mm;
        }

        .pdf-content {
            /* Zone de contenu (corps) entre l'en-tête et le pied */
        }

        {$customCss}
    </style>
</head>
<body>
    <div class="pdf-watermark">APERÇU</div>

    <div class="pdf-header">
        <div class="pdf-header-zone pdf-header-zone-1">
            {$headerHtmlZone1}
        </div>
        <div class="pdf-header-zone pdf-header-zone-2">
            {$headerHtmlZone2}
        </div>
        <div class="pdf-header-zone pdf-header-zone-3">
            {$headerHtmlZone3}
        </div>
    </div>

    <div class="pdf-footer">
        {$footerHtml}
    </div>

    <div class="pdf-content">
        {$bodyContent}
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Remplace les shortcodes par leurs valeurs.
     *
     * @param string $content
     * @param array<string, mixed> $ofIdentity
     * @return string
     */
    private static function replaceShortcodes(string $content, array $ofIdentity): string
    {
        // Shortcodes Organisme de formation
        $repNom = $ofIdentity['representant_nom'] ?? '';
        $repPrenom = $ofIdentity['representant_prenom'] ?? '';
        $repFull = trim($repPrenom . ' ' . $repNom);

        $replacements = [
            '[of:raison_sociale]'       => $ofIdentity['raison_sociale'] ?? '',
            '[of:nom_commercial]'       => $ofIdentity['nom_commercial'] ?? '',
            '[of:adresse]'              => $ofIdentity['adresse'] ?? '',
            '[of:code_postal]'          => $ofIdentity['code_postal'] ?? '',
            '[of:ville]'                => $ofIdentity['ville'] ?? '',
            '[of:telephone_fixe]'       => $ofIdentity['telephone_fixe'] ?? '',
            '[of:telephone_portable]'   => $ofIdentity['telephone_portable'] ?? '',
            '[of:email_contact]'        => $ofIdentity['email_contact'] ?? '',
            '[of:site_web]'             => $ofIdentity['site_internet'] ?? '',
            '[of:siret]'                => $ofIdentity['siret'] ?? '',
            '[of:code_ape]'             => $ofIdentity['code_ape'] ?? '',
            '[of:nda]'                  => $ofIdentity['nda'] ?? '',
            '[of:tva_intra]'            => $ofIdentity['tva_intracom'] ?? '',
            '[of:representant_legal]'   => $repFull !== '' ? $repFull : ($ofIdentity['representant_legal'] ?? ''),
            '[of:habilitation_inrs]'    => $ofIdentity['habilitation_inrs'] ?? '',
        ];

        // Logo
        if (!empty($ofIdentity['logo_id'])) {
            $logoUrl = wp_get_attachment_image_url((int) $ofIdentity['logo_id'], 'medium');
            if ($logoUrl) {
                $replacements['[of:logo]'] = '<img src="' . esc_url($logoUrl) . '" alt="Logo" style="max-height:15mm;" />';
            } else {
                $replacements['[of:logo]'] = '';
            }
        } else {
            $replacements['[of:logo]'] = '';
        }

        // Shortcodes Document (valeurs de démonstration)
        $replacements['[document:titre]'] = 'Convention de formation';
        $replacements['[document:type]'] = 'Convention';
        $replacements['[document:numero]'] = 'CONV-2024-001';
        $replacements['[document:date_creation]'] = wp_date('d/m/Y');
        $replacements['[document:date_jour]'] = wp_date('d/m/Y');
        $replacements['[document:page_courante]'] = '<span class="page-number"></span>';
        $replacements['[document:total_pages]'] = '<span class="page-count"></span>';

        // Shortcodes Client (valeurs de démonstration)
        $replacements['[client:raison_sociale]'] = 'Entreprise Exemple SARL';
        $replacements['[client:adresse]'] = '123 rue de la Démonstration';
        $replacements['[client:code_postal]'] = '75001';
        $replacements['[client:ville]'] = 'Paris';
        $replacements['[client:siret]'] = '12345678901234';
        $replacements['[client:contact_nom]'] = 'Jean Dupont';
        $replacements['[client:contact_email]'] = 'contact@exemple.fr';

        // Shortcodes Session (valeurs de démonstration)
        $replacements['[session:intitule]'] = 'Formation exemple';
        $replacements['[session:date_debut]'] = wp_date('d/m/Y', strtotime('+7 days'));
        $replacements['[session:date_fin]'] = wp_date('d/m/Y', strtotime('+14 days'));
        $replacements['[session:duree_heures]'] = '21';
        $replacements['[session:lieu]'] = 'Paris';
        $replacements['[session:objectifs]'] = 'Objectifs pédagogiques de la formation';
        $replacements['[session:tarif_ht]'] = '1 500,00 €';
        $replacements['[session:tarif_ttc]'] = '1 800,00 €';

        // Shortcodes Stagiaire (valeurs de démonstration)
        $replacements['[stagiaire:civilite]'] = 'M.';
        $replacements['[stagiaire:nom]'] = 'Martin';
        $replacements['[stagiaire:prenom]'] = 'Pierre';
        $replacements['[stagiaire:nom_complet]'] = 'Pierre Martin';
        $replacements['[stagiaire:email]'] = 'pierre.martin@exemple.fr';
        $replacements['[stagiaire:telephone]'] = '06 12 34 56 78';

        // Shortcodes Formateur (valeurs de démonstration)
        $replacements['[formateur:civilite]'] = 'Mme';
        $replacements['[formateur:nom]'] = 'Durand';
        $replacements['[formateur:prenom]'] = 'Marie';
        $replacements['[formateur:nom_complet]'] = 'Marie Durand';
        $replacements['[formateur:email]'] = 'marie.durand@exemple.fr';
        $replacements['[formateur:telephone]'] = '06 98 76 54 32';

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    private static function splitHeaderZones(string $raw): array
    {
        if ($raw === '') {
            return ['', '', ''];
        }

        $hasZone1 = strpos($raw, '[ZONE1]') !== false;
        $hasZone2 = strpos($raw, '[ZONE2]') !== false;
        $hasZone3 = strpos($raw, '[ZONE3]') !== false;

        if (!$hasZone1 && !$hasZone2 && !$hasZone3) {
            return [$raw, '', ''];
        }

        $pattern = '/\[ZONE([123])]/';
        $parts = preg_split($pattern, $raw, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false || count($parts) === 0) {
            return [$raw, '', ''];
        }

        $zones = [
            1 => '',
            2 => '',
            3 => '',
        ];

        $zones[1] .= (string) $parts[0];

        $count = count($parts);
        for ($i = 1; $i + 1 < $count; $i += 2) {
            $zoneIndex = (int) $parts[$i];
            $text = (string) $parts[$i + 1];

            if ($zoneIndex >= 1 && $zoneIndex <= 3) {
                $zones[$zoneIndex] .= $text;
            } else {
                $zones[1] .= $text;
            }
        }

        return [
            trim($zones[1]),
            trim($zones[2]),
            trim($zones[3]),
        ];
    }

    /**
     * Génère un contenu de démonstration pour l'aperçu.
     *
     * @param array<string, mixed> $ofIdentity
     * @return string
     */
    private static function getDemoContent(array $ofIdentity): string
    {
        return <<<HTML
<div style="border: 1px solid red; padding: 150px 20px; text-align: center; font-size: 14pt;">
    Ici le contenu de vos documents généré automatiquement.
</div>
HTML;
    }

    /**
     * Génère le PDF avec Dompdf.
     *
     * @param string $html
     * @param array<string, mixed> $template
     */
    private static function generatePdf(string $html, array $template): void
    {
        // Vérifier si Dompdf est disponible
        if (!class_exists('Dompdf\Dompdf')) {
            // Fallback : afficher le HTML directement
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Afficher le PDF dans le navigateur
        $dompdf->stream(
            'apercu-' . sanitize_title($template['name']) . '.pdf',
            ['Attachment' => false]
        );
        exit;
    }
}
