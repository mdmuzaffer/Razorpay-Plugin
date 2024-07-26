<?php
/*
Plugin Name: Online Yoga Classes
Description: A plugin for users to register for online yoga classes.
Version: 1.0
Author: Muzaffer
License: GPL2
*/


// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'razorpay/Razorpay.php';
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;
use Razorpay\Api\Errors\ServerError;

// Start session if not already started
function oyc_start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
add_action('init', 'oyc_start_session');


// Register activation hook
function oyc_activate() {
    // Create tables for storing registrations and payments
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table for storing yoga class registrations
    $registrations_table = $wpdb->prefix . 'yoga_class_registrations';
    $sql1 = "CREATE TABLE $registrations_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        age varchar(255) NOT NULL,
        gender varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        address varchar(255) NOT NULL,
        state varchar(255) NOT NULL,
        department varchar(255) NOT NULL,
        month varchar(255) NOT NULL,
        created_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Table for storing yoga class payments
    $payments_table = $wpdb->prefix . 'yoga_class_payments';
    $sql2 = "CREATE TABLE $payments_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        registration_id mediumint(9) NOT NULL,
        payment_status varchar(255) NOT NULL,
        transaction_id varchar(255) NOT NULL,
        order_id varchar(255) NOT NULL,
        amount decimal(10,2) NOT NULL,
        payment_reason TEXT NULL,
        payment_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    dbDelta($sql2);
}
register_activation_hook(__FILE__, 'oyc_activate');


// Register deactivation hook


function oyc_deactivate() {
    // Drop the table on plugin deactivation
    global $wpdb;
    $table_name = $wpdb->prefix . 'yoga_class_registrations';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);

    /*
    $table_name2 = $wpdb->prefix . 'yoga_class_payments';
    $sql2 = "DROP TABLE IF EXISTS $table_name2;";
    $wpdb->query($sql2);

    */
}
register_deactivation_hook(__FILE__, 'oyc_deactivate');



// Shortcode for the registration form
function oyc_registration_form() {
    global $errors;
    ob_start();
    session_start();

    if (isset($_SESSION['oyc_errors'])) {
        $errors = $_SESSION['oyc_errors'];
        unset($_SESSION['oyc_errors']);
    }

    ?>


    <?php if (isset($_SESSION['bs_registration_error']) && !empty($_SESSION['bs_registration_error'])) : ?>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-center-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.error("<?php echo esc_js($_SESSION['bs_registration_error']); ?>");
            <?php unset($_SESSION['bs_registration_error']); ?>
        });
    </script>
<?php endif; ?>

    
<div class="mailHeading">
    <h1>Arogyadhama Online Yoga Classes</h1>
    <p>The classes begins on 1st of Every Month.
        Monday to Friday 6:00 AM - 7:00 AM</p>
        <p>Please feel free to Contact Us.&nbsp;&nbsp;&nbsp;</p>
        <span>Mr. Sandeep-  +91- 96113 44691</span><br>
        <span>Mr Panda- +91- 99160 25410 &nbsp;&nbsp;&nbsp;&nbsp;</span>
</div>

<div class="main-form">
    <form id="registration-form-yoga" class="form-horizontal" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
    <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
        <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
            <label for="name" class="col-sm-2 control-label">Name*</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" placeholder="Name" minlength="2" required>
                <span id="help-block-name" class="help-block">
                    <?php if (isset($errors['name'])): ?>
                       <?php echo $errors['name']; ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email" class="col-sm-2 control-label">Email*</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
                <span id="help-block-email" class="help-block">
                    <?php if (isset($errors['email'])): ?>
                        <?php echo $errors['email']; ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['age']) ? 'has-error' : ''; ?>">
            <label for="age" class="col-sm-2 control-label">Age*</label>
            <div class="col-sm-10">
                <input type="number" id="numericAge" class="form-control" name="age" placeholder="Age" min="1" max="120" required >
                
                <span id="help-block-age" class="help-block">
                    <?php if (isset($errors['age'])): ?>
                        <?php echo $errors['age']; ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['gender']) ? 'has-error' : ''; ?>">
            <label for="gender" class="col-sm-2 control-label">Gender*</label>
            <div class="col-sm-10">
                <input type="radio" name="gender" value="male" required> Male  &nbsp;
                <input type="radio" name="gender" value="female" required> Female
                
                <span id="help-block-gender" class="help-block">
                <?php if (isset($errors['gender'])): ?>
                    <?php echo $errors['gender']; ?>
                <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['phone']) ? 'has-error' : ''; ?>">
            <label for="phone" class="col-sm-2 control-label">Phone no*</label>
            <div class="col-sm-10">
                <input type="text" id="numeric-only" class="form-control" name="phone" placeholder="Phone" minlength="10" maxlength="10" required>
                
                <span id="help-block-phone" class="help-block">
                <?php if (isset($errors['phone'])): ?>
                    <?php echo $errors['phone']; ?>
                <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['address']) ? 'has-error' : ''; ?>">
            <label for="address" class="col-sm-2 control-label">Address *</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="address" placeholder="Address"  required>
                <span id="help-block-address" class="help-block">
                    <?php if (isset($errors['address'])): ?>
                        <?php echo $errors['address']; ?>                
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['state']) ? 'has-error' : ''; ?>">
            <label for="state" class="col-sm-2 control-label">State*</label>
            <div class="col-sm-10">
                <select class="form-control" name="state" required>
                    <option value="">Select</option>
                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                    <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                    <option value="Assam">Assam</option>
                    <option value="Bihar">Bihar</option>
                    <option value="Chhattisgarh">Chhattisgarh</option>
                    <option value="Goa">Goa</option>
                    <option value="Gujarat">Gujarat</option>
                    <option value="Haryana">Haryana</option>
                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                    <option value="Jharkhand">Jharkhand</option>
                    <option value="Karnataka">Karnataka</option>
                    <option value="Kerala">Kerala</option>
                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Manipur">Manipur</option>
                    <option value="Meghalaya">Meghalaya</option>
                    <option value="Mizoram">Mizoram</option>
                    <option value="Nagaland">Nagaland</option>
                    <option value="Odisha">Odisha</option>
                    <option value="Punjab">Punjab</option>
                    <option value="Rajasthan">Rajasthan</option>
                    <option value="Sikkim">Sikkim</option>
                    <option value="Tamil Nadu">Tamil Nadu</option>
                    <option value="Telangana">Telangana</option>
                    <option value="Tripura">Tripura</option>
                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                    <option value="Uttarakhand">Uttarakhand</option>
                    <option value="West Bengal">West Bengal</option>
                    <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                    <option value="Chandigarh">Chandigarh</option>
                    <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                    <option value="Lakshadweep">Lakshadweep</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Puducherry">Puducherry</option>
                    <option value="Ladakh">Ladakh</option>
                    <option value="Jammu and Kashmir">Jammu and Kashmir</option>

                </select>
                
                <span id="help-block-state" class="help-block">
                    <?php if (isset($errors['state'])): ?>
                        <?php echo $errors['state']; ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group <?php echo isset($errors['department']) ? 'has-error' : ''; ?>">
            <label for="department" class="col-sm-2 control-label">Department*</label>
            <div class="col-sm-10">
                <select class="form-control" name="department">
                    <option value="" >Select</option>
                    <option value=" Neurology & Oncology"> Neurology & Oncology</option>
                    <option value="Cardiology & Pulmonology">Cardiology & Pulmonology</option>
                    <option value="Psychology & Psychiatry">Psychology & Psychiatry</option>
                    <option value="Rheumatology (Joints Pain)">Rheumatology (Joints Pain)</option>
                    <option value="Spinal Disorder (Back Pain)">Spinal Disorder (Back Pain)</option>
                    <option value="Metabolic Disorder(Diabetes">Metabolic Disorder(Diabetes</option>
                    <option value=" Gastroenterology(Gastritis)"> Gastroenterology(Gastritis)</option>
                    <option value="Endocrinology(Obesity)">Endocrinology(Obesity)</option>
                    <option value="Promotion of Positive Health">Promotion of Positive Health</option>
                </select>
                
                <span id="help-block-department" class="help-block">
                <?php if (isset($errors['department'])): ?>
                    <?php echo $errors['department']; ?>
                <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group d-flex align-items-end <?php echo isset($errors['month']) ? 'has-error' : ''; ?>">
            <label for="month" class="col-sm-2 control-label">Month*</label>
            <div class="col-sm-10">
                <input type="radio" name="month" value="<?php echo date('F'); ?>" required > <?php echo date('F'); ?> &nbsp;
                <input type="radio" name="month" value="<?php echo date('F', strtotime('+1 month')); ?>" required> <?php echo date('F', strtotime('+1 month')); ?>
                
                <span id="help-block-month" class="help-block">
                <?php if (isset($errors['month'])): ?>
                    <?php echo $errors['month']; ?>
                <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" name="oyc_submit" value="Register">
            </div>
        </div>

    </form>
</div>

    <?php
    return ob_get_clean();
}
add_shortcode('oyc_registration_form', 'oyc_registration_form');


function oyc_handle_form_submission() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_POST['oyc_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'yoga_class_registrations';

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $age = intval($_POST['age']);
        $gender = sanitize_text_field($_POST['gender']);
        $phone = sanitize_text_field($_POST['phone']);
        $address = sanitize_text_field($_POST['address']);
        $state = sanitize_text_field($_POST['state']);
        $department = sanitize_text_field($_POST['department']);
        $month = sanitize_text_field($_POST['month']);

        // Validate required fields
        $errors = [];


       if (empty($phone)) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!is_numeric($phone)) {
            $errors['phone'] = 'Phone number must contain only numeric characters';
        } elseif (strlen($phone) < 10 || strlen($phone) > 12) {
            $errors['phone'] = 'Phone number must be between 10 and 12 characters';
        }


        /* if (empty($phone)) {
            $errors['phone'] = 'Phone number is required';
        }*/

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        if (empty($email) || !is_email($email)) {
            $errors['email'] = 'Valid email is required';
        }
        if (empty($age) || $age <= 0) {
            $errors['age'] = 'Valid age is required';
        }
        if (empty($gender)) {
            $errors['gender'] = 'Gender is required';
        }
        if (empty($address)) {
            $errors['address'] = 'Address is required';
        }
        if (empty($state)) {
            $errors['state'] = 'State is required';
        }
        if (empty($department)) {
            $errors['department'] = 'Department is required';
        }
        if (empty($month)) {
            $errors['month'] = 'Month is required';
        }


        if (empty($errors)) {
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $name,
                    'email' => $email,
                    'age' => $age,
                    'gender' => $gender,
                    'phone' => $phone,
                    'address' => $address,
                    'state' => $state,
                    'department' => $department,
                    'month' => $month,
                )
            );

            $registeredId = $wpdb->insert_id;

            $amount = 2000;

            // Razorpay integration
            try {
                $api = new Api("rzp_test_sK5WTllxyqg3MB", "0QdT1rF6im1jwPurXa4WAPNy");
                $orderData = [
                    'receipt'         => 'order_rcptid_11',
                    'amount'          => $amount * 100, // amount in the smallest currency unit
                    'currency'        => 'INR',
                    'payment_capture' => 1
                ];

                $razorpayOrder = $api->order->create($orderData);
                $_SESSION['razorpay_order_id'] = $razorpayOrder['id'];
                $razorpayOrderId = $razorpayOrder['id'];

                $data = payment_cart($razorpayOrderId, $amount, $name, $email, $phone ,$address, $registeredId);
                exit;

            } catch (BadRequestError $e) {
                // Handle invalid request error
                echo 'Error: ' . $e->getMessage();
            } catch (ServerError $e) {
                // Handle server error
                echo 'Server error: ' . $e->getMessage();
            } catch (Exception $e) {
                // Handle other types of exceptions
                echo 'An error occurred: ' . $e->getMessage();
            }

        } else {
            $_SESSION['oyc_errors'] = $errors;
            wp_redirect($_SERVER['REQUEST_URI']);
            exit;
        }
    }
}
add_action('template_redirect', 'oyc_handle_form_submission');


function bs_enqueue_toastrmsg() {
    wp_enqueue_style('toastrCss', plugin_dir_url( __FILE__ ) .'assets/css/toastr.min.css');
    wp_enqueue_script('toastrJs', plugin_dir_url( __FILE__ ) . 'assets/js/toastr.min.js', array('jquery'), null, true);
    wp_enqueue_style('customCss', plugin_dir_url( __FILE__ ) .'assets/css/custom.css');
    wp_enqueue_style('customScreen', plugin_dir_url( __FILE__ ) .'assets/css/screen.css');
    wp_enqueue_script('my-plugin-customscript', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array('jquery'), null, true);
    wp_enqueue_script('my-plugin-validate', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.validate.min.js', array('jquery'), null, true);

     /*wp_enqueue_script('my-pluginScript', plugin_dir_url( __FILE__ ) . 'assets/js/jquery-3.7.1.min.js', array('jquery'), null, true);*/
}
add_action('wp_enqueue_scripts', 'bs_enqueue_toastrmsg');

function bs_enqueue_admin_assets($hook) {
    wp_enqueue_style('bootstrapCSS', plugin_dir_url( __FILE__ ) .'assets/css/bootstrap.min.css');
}
add_action('admin_enqueue_scripts', 'bs_enqueue_admin_assets');



function payment_cart($razorpayOrderId, $amount, $name, $email, $phone, $address, $registeredId) {


    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        var razorpayOrderId = '<?php echo $razorpayOrderId; ?>';
        if (razorpayOrderId !== '') {
            var options = {
                key: 'rzp_test_sK5WTllxyqg3MB',  // Replace with your Razorpay key ID
                amount: <?php echo $amount * 100; ?>,  // Amount in paise
                currency: 'INR',
                name: 'Arogyadhma',
                description: 'Online yoga class',
                order_id: razorpayOrderId,
                image: 'https://arogyadhama2.sdnaprod.com/wp-content/uploads/2022/10/Untitled-1-copy.png',
                handler: function(response) {
                    var paymentStatus = response.razorpay_payment_id ? 'success' : 'fail';

                    // Send payment status to server via AJAX
                    jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'process_razorpay_payment',
                            payment_status: paymentStatus,
                            transaction_id: response.razorpay_payment_id,
                            order_id: razorpayOrderId,
                            amount: '<?php echo $amount; ?>',
                            registeredId: '<?php echo $registeredId; ?>',
                        },
                        success: function(response) {
                           console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });

                    // Redirect or perform further actions after successful payment
                    if (paymentStatus =='success') {

                       <?php $_SESSION['bs_registration_success'] = 'Successfully registered for yoga!'; ?>
                        toastr.success('Payment successful! Redirecting...', 'Success');
                        setTimeout(function() {
                            window.location.href = "/thank-you/";
                        }, 3000);
                    }
                },
                prefill: {
                    name: '<?php echo $name; ?>',
                    email: '<?php echo $email; ?>',
                    contact: '<?php echo $phone; ?>'
                },
                notes: {
                    address: '<?php echo $address; ?>',
                    order_id: razorpayOrderId
                },
                modal: {
                        "ondismiss": function() {
                            toastr.info('Payment cancelled');


                            jQuery.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'process_razorpay_payment',
                                    payment_status: 'cancelled',
                                    transaction_id: null,
                                    order_id: razorpayOrderId,
                                    amount: '<?php echo $amount; ?>',
                                    registeredId: '<?php echo $registeredId; ?>',
                                },
                                success: function(response) {
                                    console.log(response);
                                    // Handle server response if needed
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                }
                            });



                            setTimeout(function() {
                                window.location.href = "/online-yoga-classes/";
                            }, 2000);
                        }
                    },
            };

            var rzp = new Razorpay(options);
            rzp.open();

            rzp.on('payment.failed', function (response){

                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'process_razorpay_payment',
                        payment_status: 'fail',
                        transaction_id: response.error.description,
                        order_id: razorpayOrderId,
                        amount: '<?php echo $amount; ?>',
                        registeredId: '<?php echo $registeredId; ?>',
                    },
                    success: function(response) {
                        console.log(response);
                        // Handle server response if needed
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                <?php
                 $_SESSION['bs_registration_error'] = 'Payment failed! Please try again'; ?>

                toastr.error('Payment failed! Please try again.', 'Error');
                setTimeout(function() {
                        window.location.href = "/online-yoga-classes/";
                    }, 6000);
            });

        }
    </script>
    <?php
}




add_action('wp_ajax_process_razorpay_payment', 'process_razorpay_payment');
add_action('wp_ajax_nopriv_process_razorpay_payment', 'process_razorpay_payment'); // If you need to handle non-logged-in users

function process_razorpay_payment() {
    global $wpdb;

    // Handle AJAX request
    if (isset($_POST['payment_status']) && isset($_POST['transaction_id']) && isset($_POST['order_id']) && isset($_POST['amount']) && isset($_POST['registeredId'])) {
        $paymentStatus = sanitize_text_field($_POST['payment_status']);
        $transaction_id = sanitize_text_field($_POST['transaction_id']);
        $order_id = sanitize_text_field($_POST['order_id']);
        $amount = sanitize_text_field($_POST['amount']);
        $registeredId = sanitize_text_field($_POST['registeredId']);

        $table_name = $wpdb->prefix . 'yoga_class_payments';
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'registration_id' => $registeredId, // Placeholder value, adjust as necessary
                'transaction_id' => $transaction_id ?? null,
                'payment_reason' => $transaction_id ?? null,
                'order_id' => $order_id,
                'amount' => $amount,
                'payment_status' => $paymentStatus,
                'payment_date' => current_time('mysql'),
            )
        );

        if ($inserted) {
            echo 'Payment successful!';
        } else {
            echo 'Payment failed!';
        }
    } else {
        echo 'Invalid request';
    }

    // Always die() at the end of your AJAX callback function.
    die();
}



function my_custom_plugin_menu() {
    add_menu_page(
        'Online Yoga Class Registration',   // Page title
        'Yoga Registration',   // Menu title
        'manage_options',      // Capability
        'custompage',          // Menu slug
        'yoga_custom_page_display', // Callback function
        'dashicons-welcome-learn-more',
        5  
    );


    // Sub-menu for new users
    add_submenu_page(
        'custompage',                   // Parent slug
        'New Users',                    // Page title
        'New Users',                    // Menu title
        'manage_options',               // Capability
        'new-users',                    // Menu slug
        'new_users_display'             // Callback function
    );

    // Sub-menu for old users
    add_submenu_page(
        'custompage',                   // Parent slug
        'Old Registered Users',         // Page title
        'Old Users',                    // Menu title
        'manage_options',               // Capability
        'old-users',                    // Menu slug
        'old_users_display'             // Callback function
    );
}
add_action('admin_menu', 'my_custom_plugin_menu');

function yoga_custom_page_display() {
    include plugin_dir_path(__FILE__) . 'template/yoga-main-class-page-content.php';
}

function new_users_display() {
    include plugin_dir_path(__FILE__) . 'template/yoga-class-page-content.php';
}

function old_users_display() {
    echo '<h4>Old Registered Users</h4>';
    include plugin_dir_path(__FILE__) . 'template/yoga-class-page-old.php';
}
