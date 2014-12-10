<?php
/**
 * QRLogin admin area.
 *
 * @package   QRLogin
 * @author    Labs64 <info@labs64.com>
 * @license   GPL-2.0+
 * @link      http://www.labs64.com
 * @copyright 2014 Labs64
 */

define('QRL_OPTIONS', 'QRL_OPTIONS');
define('QRL_API_KEY', '31c7bc4e-90ff-44fb-9f07-b88eb06ed9dc');

class QRLogin_Admin
{

    /**
     * Instance of this class.
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     */
    private function __construct()
    {

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin = QRLogin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));

        // Add the options page and menu item.
        add_action('admin_init', array($this, 'admin_page_init'));
    }

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles()
    {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/qrl-admin.css', __FILE__), array(), QRLogin::VERSION);
        }

    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts()
    {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/qrl-admin.js', __FILE__), array('jquery'), QRLogin::VERSION);
        }

    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     */
    public function add_plugin_admin_menu()
    {

        $this->plugin_screen_hook_suffix = add_options_page(
            __('QRLogin', $this->plugin_slug),
            __('QRLogin', $this->plugin_slug),
            'manage_options',
            $this->plugin_slug,
            array($this, 'display_plugin_admin_page')
        );

    }

    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_admin_page()
    {
        ?>
        <div class="wrap" xmlns="http://www.w3.org/1999/html">
            <a href="http://www.labs64.com" target="_blank" class="icon-labs64 icon32"></a>

            <h2><?php _e('QRLogin by Labs64', $this->plugin_slug); ?></h2>

            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('QRL_OPTIONS_GROUP');
                $this->qrl_settings_fields_hidden();
                do_settings_sections($this->plugin_slug);
                submit_button();
                ?>
            </form>
            <hr/>
            <?php
            $this->qrl_print_reference_section();
            ?>
        </div>
        <div class="info_menu">
            <?php
            $this->qrl_print_feedback_section();
            ?>
        </div>
    <?php
    }

    /**
     * Print sections divider
     */
    public function qrl_print_divider()
    {
        ?>
        <hr/>
    <?php
    }

    /**
     * Print the Common-Section info text
     */
    public function qrl_print_common_section_info()
    {
    }

    /**
     * Print the feedback section
     */
    public function qrl_print_feedback_section()
    {
        ?>
        <h3><?php _e('Feedback', $this->plugin_slug); ?></h3>

        <p><?php _e('Did you find a bug? Have an idea for a plugin? Please help us improve this plugin', $this->plugin_slug); ?>
            :</p>
        <ul>
            <li>
                <a href="https://github.com/Labs64/qr-login/issues"
                   target="_blank"><?php _e('Report a bug, or suggest an improvement', $this->plugin_slug); ?></a>
            </li>
            <li><a href="http://www.facebook.com/labs64" target="_blank"><?php _e('Like us on Facebook'); ?></a>
            </li>
            <li><a href="http://www.labs64.com/blog" target="_blank"><?php _e('Read Labs64 Blog'); ?></a></li>
        </ul>
    <?php
    }

    /**
     * Print the reference section
     */
    public function qrl_print_reference_section()
    {
    }

    /**
     * Add settings action link to the plugins page.
     */
    public function add_action_links($links)
    {

        return array_merge(
            array(
                'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . __('Settings', $this->plugin_slug) . '</a>'
            ),
            $links
        );

    }

    /**
     * Register and add settings
     */
    public function admin_page_init()
    {
        register_setting(
            'QRL_OPTIONS_GROUP',
            QRL_OPTIONS,
            array($this, 'qrl_sanitize_fields')
        );

        add_settings_section(
            'QRL_COMMON_SETTINGS',
            __('QRLogin Settings', $this->plugin_slug),
            array($this, 'qrl_print_common_section_info'),
            $this->plugin_slug
        );

        add_settings_field(
            'qrl_netlicensing_apikey',
            __('NetLicensing APIKey', $this->plugin_slug),
            array($this, 'qrl_text_field_callback'),
            $this->plugin_slug,
            'QRL_COMMON_SETTINGS',
            array(
                'id' => 'qrl_netlicensing_apikey',
                'description' => __('To use the NetLicensing you need to have an APIKey. ' . 'See <a href="https://www.labs64.de/confluence/x/pwCo#NetLicensingAPI(RESTful)-APIKeyIdentification" target="_blank">here</a>' . ' for more details.', $this->plugin_slug),
            )
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function qrl_sanitize_fields($input)
    {
        if (isset($input['qrl_netlicensing_apikey'])) {
            $input['qrl_netlicensing_apikey'] = sanitize_text_field($input['qrl_netlicensing_apikey']);
        }

        return $input;
    }

    /**
     */
    public function qrl_settings_fields_hidden()
    {
        $this->qrl_print_settings_field_hidden('qrl_option2');
    }

    /**
     */
    public function qrl_print_settings_field_hidden($id)
    {
        $value = $this->qrl_get_single_option($id);
        echo "<input type='hidden' id='$id' name='QRL_OPTIONS[$id]' value='$value' />";
    }

    /**
     */
    public function qrl_text_field_callback($args)
    {
        $id = $args['id'];
        $description = $args['description'];
        $value = $this->qrl_get_single_option($id);
        echo "<input type='text' id='$id' name='QRL_OPTIONS[$id]' value='$value' class='regular-text' />";
        echo "<p class='description'>$description</p>";
    }

    /**
     */
    public function qrl_checkbox_field_callback($args)
    {
        $id = $args['id'];
        $caption = $args['caption'];
        $description = $args['description'];
        $value = $this->qrl_get_single_option($id);
        echo "<input type='checkbox' id='$id' name='QRL_OPTIONS[$id]' value='1' class='code' " . checked(1, $value, false) . " /> $caption";
        echo "<p class='description'>$description</p>";
    }

    /**
     * Returns default options.
     * If you override the options here, be careful to use escape characters!
     */
    public function qrl_get_default_options()
    {
        $default_options = array(
            'qrl_netlicensing_apikey' => '',
            'qrl_option2' => '0'
        );
        return $default_options;
    }

    /**
     * Retrieves (and sanitises) options
     */
    public function qrl_get_options()
    {
        $options = $this->qrl_get_default_options();
        $stored_options = get_option(QRL_OPTIONS);
        if (!empty($stored_options)) {
            $this->qrl_sanitize_fields($stored_options);
            $options = wp_parse_args($stored_options, $options);
        }
        update_option(QRL_OPTIONS, $options);
        return $options;
    }

    /**
     * Retrieves single option
     */
    public function qrl_get_single_option($name)
    {
        $options = $this->qrl_get_options();
        return $options[$name];
    }

    /**
     * Set single option value
     */
    public function qrl_set_single_option($name, $value)
    {
        $options = $this->qrl_get_options();
        $options[$name] = $value;
        update_option(QRL_OPTIONS, $options);
    }

}
