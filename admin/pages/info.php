<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

function display_info_page() {
    ?>
    <div class="wrap">
        <h1>Plugin Information</h1>
        
        <h2>About the Plugin</h2>
        <p>This plugin offers various functionalities to manage RSVPs for private events efficiently.</p>

        <h2>Donation</h2>
        <p>If you find this plugin useful, consider supporting its development:</p>
        <form action="https://www.paypal.com/donate" method="post" target="_top">
            <input type="hidden" name="hosted_button_id" value="44NH2X79N2QX4" />
            <input type="submit" class="paypal_donate" name="submit" value="Donate" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
        </form>
        
        <h2>Contact</h2>
        <p>For support or inquiries, fell free to contact me at:</p>
        <p>Email: <a href="mailto:codeverse93@gmail.com">codeverse93@gmail.com</a></p>
        
        <h2>Version</h2>
        <p>Current Version: 1.1</p>
    </div>
    <?php
}

?>