<?php



// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>


<div class="markdown prose w-full break-words dark:prose-invert light">
    <p>Here is the complete plugin description, shortcode implementation, and a list of features:</p>
    <h5>Description</h5>
    <p>
        <strong>Plugin Name:</strong> Online Yoga Classes<br />
        <strong>Description:</strong> A plugin for users to register for online yoga classes.<br />
        <strong>Version:</strong> 1.0<br />
        <strong>Author:</strong> Muzaffer<br />
        <strong>License:</strong> GPL2 <br />
        <strong>Support:</strong> SDNA TECH
    </p>
    <h5>Shortcode</h5>
    <p>The plugin includes a shortcode to display the registration form on any page or post.</p>
    <p><strong>Shortcode:</strong> <code>[oyc_registration_form]</code></p>
    <p>You can add this shortcode to any WordPress post or page to display the registration form.</p>
    <h5>Features</h5>
    <ol>
        <li>
            <p><strong>User Registration Form:</strong></p>
            <ul>
                <li>Users can register for online yoga classes.</li>
                <li>Form fields include Name, Email, Age, Gender, Phone, Address, State, Department, and Month.</li>
            </ul>
        </li>
        <li>
            <p><strong>Razorpay Integration:</strong></p>
            <ul>
                <li>Integrated payment processing with Razorpay.</li>
                <li>Handles payment status and redirects users upon successful payment.</li>
            </ul>
        </li>
        <li>
            <p><strong>Database Tables:</strong></p>
            <ul>
                <li>Creates custom tables for storing yoga class registrations and payments.</li>
                <li>Deletes these tables upon plugin deactivation.</li>
            </ul>
        </li>
        <li>
            <p><strong>Note:</strong></p>
            <ul>
                <li>Create a thank you page url should be ('/thank-you/') it will automatically redirect in thank you page.</li>
            </ul>
        </li>
    </ol>
</div>