<div class="wrap">
    <h2>xAPI shortcodes</h2>

    <h3>About</h3>
    <p>
        This plugin enables a number of shortcodes which can be used to show information from an xAPI 
        Learning Record Store.
    </p>

    <h3>xAPI Endpoint Settings</h3>
    <p>
        The settings in this section specifies the URL and credentials when connecting to
        the LRS to fetch information.
    </p>
    <form method="post" action="options.php">
        <?php settings_fields('xapisc'); ?>
        <?php do_settings_sections('xapisc'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">xAPI Endpoint URL</th>
                <td>
                    <input type="text" name="xapisc_endpoint_url" 
                        value="<?php echo esc_attr(get_option("xapisc_endpoint_url")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">xAPI Username</th>
                <td>
                    <input type="text" name="xapisc_username" 
                        value="<?php echo esc_attr(get_option("xapisc_username")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">xAPI Password</th>
                <td>
                    <input type="text" name="xapisc_password" 
                        value="<?php echo esc_attr(get_option("xapisc_password")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>