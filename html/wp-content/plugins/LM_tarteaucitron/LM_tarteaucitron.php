<?php

/**
 * @package LM_tarteaucitron
 * @version 1.0.1
 */

/*
Plugin Name: LM_tarteaucitron
Plugin URI: https://github.com/AmauriC/tarteaucitron.js/
Description: Ce plugin sert à lancer le script "tarte au citron" pour les cookies.
Author: Florian Lavigne Chappaz
Version: 1.0.1
Author URI: https://florian-lvgchp.netlify.app
*/

function tarteaucitron()
{
    wp_enqueue_script(
        'tarteaucitron',
        plugin_dir_url(__FILE__) . 'tarteaucitron/tarteaucitron.min.js',
        array(),
        null,
        true
    );

    $options = get_option('tarteaucitron_options', []);

    $defaults = [
        'hashtag' => '#tarteaucitron',
        'highPrivacy' => 0,
        'AcceptAllCta' => 0,
        'orientation' => 'bottom',
        'adblocker' => 0,
        'showAlertSmall' => 0,
        'cookieslist' => 0
    ];
    $options = wp_parse_args($options, $defaults);

    $script = "
        tarteaucitron.init({
            hashtag: '" . esc_js($options['hashtag']) . "',
            highPrivacy: " . ($options['highPrivacy'] ? 'true' : 'false') . ",
            AcceptAllCta: " . ($options['AcceptAllCta'] ? 'true' : 'false') . ",
            orientation: '" . esc_js($options['orientation']) . "',
            adblocker: " . ($options['adblocker'] ? 'true' : 'false') . ",
            showAlertSmall: " . ($options['showAlertSmall'] ? 'true' : 'false') . ",
            cookieslist: " . ($options['cookieslist'] ? 'true' : 'false') . "
        });
    ";

    wp_add_inline_script('tarteaucitron', $script);
}

function plugin_menu()
{
    add_menu_page(
        'Configuration du plugin',
        'Tarte au citron',
        'manage_options',
        'tarteaucitron',
        'tarteaucitron_plugin',
        'dashicons-admin-plugins'
    );
}

function validate_tarteaucitron_options($input)
{
    $valid = [];
    $valid['hashtag'] = sanitize_text_field($input['hashtag'] ?? '#tarteaucitron');
    $valid['highPrivacy'] = isset($input['highPrivacy']) ? (int) $input['highPrivacy'] : 0;
    $valid['AcceptAllCta'] = isset($input['AcceptAllCta']) ? (int) $input['AcceptAllCta'] : 0;
    $valid['orientation'] = sanitize_text_field($input['orientation'] ?? 'bottom');
    $valid['adblocker'] = isset($input['adblocker']) ? (int) $input['adblocker'] : 0;
    $valid['showAlertSmall'] = isset($input['showAlertSmall']) ? (int) $input['showAlertSmall'] : 0;
    $valid['cookieslist'] = isset($input['cookieslist']) ? (int) $input['cookieslist'] : 0;

    return $valid;
}

function plugin_option()
{
    register_setting(
        'tarteaucitron_options_group',
        'tarteaucitron_options',
        'validate_tarteaucitron_options'
    );
}

function tarteaucitron_plugin()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('tarteaucitron_options', []);
?>
    <div class="wrap">
        <h1>Tarte au citron - Configuration du plugin</h1>
        <p><strong>Remplissez les contenus des scripts d'initialisation de Tarteaucitron.js.</strong></p>
        <form method="POST" action="options.php" style="display: flex; flex-direction: column; gap: 1rem;">
            <?php
            settings_fields('tarteaucitron_options_group');
            ?>
            <div>
                <label for="hashtag">hashtag</label>
                <input type="text" name="tarteaucitron_options[hashtag]" value="<?php echo esc_attr($options['hashtag'] ?? '#tarteaucitron'); ?>">
            </div>
            <div>
                <label for="highPrivacy">highPrivacy</label>
                <select name="tarteaucitron_options[highPrivacy]">
                    <option value="1" <?php selected((int) ($options['highPrivacy'] ?? 0), 1); ?>>true</option>
                    <option value="0" <?php selected((int) ($options['highPrivacy'] ?? 0), 0); ?>>false</option>
                </select>
            </div>
            <div>
                <label for="AcceptAllCta">AcceptAllCta</label>
                <select name="tarteaucitron_options[highPrivacy]">
                    <option value="1" <?php selected((int) ($options['AcceptAllCta'] ?? 0), 1); ?>>true</option>
                    <option value="0" <?php selected((int) ($options['AcceptAllCta'] ?? 0), 0); ?>>false</option>
                </select>
            </div>
            <div>
                <label for="orientation">orientation</label>
                <input type="text" name="tarteaucitron_options[orientation]" value="<?php echo esc_attr($options['orientation'] ?? 'bottom'); ?>">
            </div>
            <div>
                <label for="adblocker">adblocker</label>
                <select name="tarteaucitron_options[highPrivacy]">
                    <option value="1" <?php selected((int) ($options['adblocker'] ?? 0), 1); ?>>true</option>
                    <option value="0" <?php selected((int) ($options['adblocker'] ?? 0), 0); ?>>false</option>
                </select>
            </div>
            <div>
                <label for="showAlertSmall">showAlertSmall</label>
                <select name="tarteaucitron_options[highPrivacy]">
                    <option value="1" <?php selected((int) ($options['showAlertSmall'] ?? 0), 1); ?>>true</option>
                    <option value="0" <?php selected((int) ($options['showAlertSmall'] ?? 0), 0); ?>>false</option>
                </select>
            </div>
            <div>
                <label for="cookieslist">cookieslist</label>
                <select name="tarteaucitron_options[highPrivacy]">
                    <option value="1" <?php selected((int) ($options['cookieslist'] ?? 0), 1); ?>>true</option>
                    <option value="0" <?php selected((int) ($options['cookieslist'] ?? 0), 0); ?>>false</option>
                </select>
            </div>
            <?php
            submit_button('Enregistrer les modifications', 'primary', 'submit', true);
            ?>
        </form>
    </div>
<?php
}

add_action('wp_enqueue_scripts', 'tarteaucitron');
add_action('admin_menu', 'plugin_menu');
add_action('admin_init', 'plugin_option');
