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
        $headerHtml = self::replaceShortcodes($template['header_html'] ?? '', $ofIdentity);
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
        $headerHeight = (float) ($template['header_height'] ?? 20);
        $footerHeight = (float) ($template['footer_height'] ?? 15);

        // Contenu de démonstration
        $bodyContent = self::getDemoContent($ofIdentity);

        // Tailles de police calculées pour les sous-titres
        $h2Size = max(10, $fontTitleSize - 2);
        $h3Size = max(9, $fontTitleSize - 4);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aperçu PDF - {$template['name']}</title>
    <style>
        @page {
            margin: {$marginTop}mm {$marginRight}mm {$marginBottom}mm {$marginLeft}mm;
        }
        
        body {
            font-family: {$fontBody};
            font-size: {$fontBodySize}pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
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
        
        .pdf-header {
            position: fixed;
            top: -{$marginTop}mm;
            left: -{$marginLeft}mm;
            right: -{$marginRight}mm;
            height: {$headerHeight}mm;
            padding: 5mm {$marginLeft}mm;
            background: {$headerBgColor};
            overflow: hidden;
        }
        
        .pdf-footer {
            position: fixed;
            bottom: -{$marginBottom}mm;
            left: -{$marginLeft}mm;
            right: -{$marginRight}mm;
            height: {$footerHeight}mm;
            padding: 3mm {$marginLeft}mm;
            background: {$footerBgColor};
            font-size: 9pt;
            color: #666;
            overflow: hidden;
        }
        
        .pdf-content {
            margin-top: {$headerHeight}mm;
            margin-bottom: {$footerHeight}mm;
        }
        
        .pdf-watermark {
            position: fixed;
            top: 35%;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 60pt;
            font-weight: bold;
            color: rgba(200, 200, 200, 0.25);
            transform: rotate(-35deg);
            z-index: -1;
            pointer-events: none;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5mm 0;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 2mm 3mm;
            text-align: left;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        {$customCss}
    </style>
</head>
<body>
    <div class="pdf-watermark">APERÇU</div>
    
    <div class="pdf-header">
        {$headerHtml}
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
        $replacements = [
            '[of:raison_sociale]'     => $ofIdentity['raison_sociale'] ?? '',
            '[of:nom_commercial]'     => $ofIdentity['nom_commercial'] ?? '',
            '[of:adresse]'            => $ofIdentity['adresse'] ?? '',
            '[of:code_postal]'        => $ofIdentity['code_postal'] ?? '',
            '[of:ville]'              => $ofIdentity['ville'] ?? '',
            '[of:telephone_fixe]'     => $ofIdentity['telephone_fixe'] ?? '',
            '[of:telephone_portable]' => $ofIdentity['telephone_portable'] ?? '',
            '[of:email_contact]'      => $ofIdentity['email_contact'] ?? '',
            '[of:site_web]'           => $ofIdentity['site_internet'] ?? '',
            '[of:siret]'              => $ofIdentity['siret'] ?? '',
            '[of:code_ape]'           => $ofIdentity['code_ape'] ?? '',
            '[of:nda]'                => $ofIdentity['nda'] ?? '',
            '[of:tva_intra]'          => $ofIdentity['tva_intracom'] ?? '',
            '[of:representant_legal]' => $ofIdentity['representant_legal'] ?? '',
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

    /**
     * Génère un contenu de démonstration pour l'aperçu.
     *
     * @param array<string, mixed> $ofIdentity
     * @return string
     */
    private static function getDemoContent(array $ofIdentity): string
    {
        $raisonSociale = $ofIdentity['raison_sociale'] ?? 'Organisme de formation';

        return <<<HTML
<h1>Convention de formation professionnelle</h1>

<h2>Entre les soussignés</h2>

<p><strong>{$raisonSociale}</strong>, organisme de formation,<br>
ci-après dénommé « l'Organisme de formation »,</p>

<p>Et</p>

<p><strong>Entreprise Exemple SARL</strong>,<br>
123 rue de la Démonstration, 75001 Paris,<br>
ci-après dénommé « le Client »,</p>

<h2>Article 1 - Objet</h2>

<p>La présente convention a pour objet la réalisation d'une action de formation intitulée :</p>

<p><strong>Formation exemple</strong></p>

<h2>Article 2 - Durée et dates</h2>

<table>
    <tr>
        <th>Durée totale</th>
        <td>21 heures</td>
    </tr>
    <tr>
        <th>Date de début</th>
        <td>À définir</td>
    </tr>
    <tr>
        <th>Date de fin</th>
        <td>À définir</td>
    </tr>
    <tr>
        <th>Lieu</th>
        <td>Paris</td>
    </tr>
</table>

<h2>Article 3 - Coût de la formation</h2>

<table>
    <tr>
        <th>Désignation</th>
        <th class="text-right">Montant HT</th>
    </tr>
    <tr>
        <td>Formation exemple - 21 heures</td>
        <td class="text-right">1 500,00 €</td>
    </tr>
    <tr>
        <th>Total HT</th>
        <td class="text-right"><strong>1 500,00 €</strong></td>
    </tr>
</table>

<h2>Article 4 - Modalités de règlement</h2>

<p>Le règlement sera effectué à réception de facture, à l'issue de la formation.</p>

<p style="margin-top: 20mm;">
    <strong>Fait en deux exemplaires, à Paris, le _______________</strong>
</p>

<table style="border: none; margin-top: 10mm;">
    <tr style="border: none;">
        <td style="border: none; width: 50%;">
            <p><strong>Pour l'Organisme de formation</strong></p>
            <p style="height: 20mm;"></p>
            <p>Signature et cachet</p>
        </td>
        <td style="border: none; width: 50%;">
            <p><strong>Pour le Client</strong></p>
            <p style="height: 20mm;"></p>
            <p>Signature et cachet</p>
        </td>
    </tr>
</table>
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
