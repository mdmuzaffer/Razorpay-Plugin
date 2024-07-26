<?php



// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


global $wpdb;
$registrations_table = $wpdb->prefix . 'yoga_class_registrations';
$payments_table = $wpdb->prefix . 'yoga_class_payments';

// Define the number of items per page
$items_per_page = 10;

// Get the current page number from the URL, if none exists, set to 1
$current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

// Calculate the offset for the query
$offset = ($current_page - 1) * $items_per_page;

// Query to get the total number of items
$total_items = $wpdb->get_var("
    SELECT COUNT(*) 
    FROM $registrations_table AS reg
    INNER JOIN $payments_table AS pay ON reg.id = pay.registration_id
");

// Query to get the items for the current page
// Query to get the items for the current page
$query = $wpdb->prepare("
    SELECT reg.*, pay.* 
    FROM $registrations_table AS reg
    INNER JOIN $payments_table AS pay ON reg.id = pay.registration_id
    WHERE reg.created_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
    ORDER BY reg.id DESC
    LIMIT %d OFFSET %d
", $items_per_page, $offset);


$results = $wpdb->get_results($query);

// Calculate the total number of pages
$total_pages = ceil($total_items / $items_per_page);

?>

<h5>New Users</h5>

<?php if ($results): ?>
<table class="table table-striped table-bordered">
    <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Department</th>
            <th scope="col">payment status</th>
            <th scope="col">transaction Id</th>
            <th scope="col">Oder Id</th>
            <th scope="col">Price</th>
            <th scope="col">Month</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row): ?>
        <tr>
            <td><?php echo esc_html($row->id); ?></td>
            <td><?php echo esc_html($row->name); ?></td>
            <td><?php echo esc_html($row->email); ?></td>
            <td><?php echo esc_html($row->department); ?></td>
            <td><?php echo esc_html($row->payment_status); ?></td>
            <td><?php echo esc_html($row->transaction_id); ?></td>
            <td><?php echo esc_html($row->order_id); ?></td>
            <td><?php echo esc_html($row->amount); ?></td>
            <td><?php echo esc_html($row->month); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
    <p>No registrations found.</p>
<?php endif; ?>

<?php
// Display pagination links if there are more than one page
if ($total_pages > 1): 
    $pagination_base = add_query_arg('paged', '%#%');
    echo paginate_links(array(
        'base' => $pagination_base,
        'format' => '?paged=%#%',
        'current' => $current_page,
        'total' => $total_pages,
    ));
endif;
?>



