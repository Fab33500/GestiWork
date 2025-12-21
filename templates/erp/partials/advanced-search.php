<?php

declare(strict_types=1);

$gw_search_fields = isset($gw_search_fields) && is_array($gw_search_fields) ? $gw_search_fields : [];
$gw_search_action_url = isset($gw_search_action_url) ? (string) $gw_search_action_url : '';
$gw_search_reset_url = isset($gw_search_reset_url) ? (string) $gw_search_reset_url : '';
$gw_search_submit_label = isset($gw_search_submit_label) ? (string) $gw_search_submit_label : __('Rechercher', 'gestiwork');
$gw_search_reset_label = isset($gw_search_reset_label) ? (string) $gw_search_reset_label : __('Réinitialiser', 'gestiwork');

?>

<form method="get" action="<?php echo esc_url($gw_search_action_url); ?>" class="gw-advanced-search-form">
    <?php foreach ($gw_search_fields as $field) : ?>
        <?php
        $type = isset($field['type']) ? (string) $field['type'] : 'text';
        $id = isset($field['id']) ? (string) $field['id'] : '';
        $name = isset($field['name']) ? (string) $field['name'] : '';
        $label = isset($field['label']) ? (string) $field['label'] : '';
        $value = isset($field['value']) ? (string) $field['value'] : '';
        $placeholder = isset($field['placeholder']) ? (string) $field['placeholder'] : '';
        $options = isset($field['options']) && is_array($field['options']) ? $field['options'] : [];
        ?>
        <?php if ($type === 'hidden') : ?>
            <input type="hidden" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php else : ?>
            <div>
                <?php if ($label !== '' && $id !== '') : ?>
                    <label class="gw-settings-placeholder" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
                <?php endif; ?>

                <?php if ($type === 'select') : ?>
                    <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="gw-modal-input">
                        <?php foreach ($options as $optionValue => $optionLabel) : ?>
                            <option value="<?php echo esc_attr((string) $optionValue); ?>" <?php selected($value, (string) $optionValue); ?>>
                                <?php echo esc_html((string) $optionLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="gw-modal-input" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" />
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="gw-advanced-search-actions">
        <?php if ($gw_search_reset_url !== '') : ?>
            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($gw_search_reset_url); ?>"><?php echo esc_html($gw_search_reset_label); ?></a>
        <?php endif; ?>
        <button type="submit" class="gw-button gw-button--primary"><?php echo esc_html($gw_search_submit_label); ?></button>
    </div>
</form>
