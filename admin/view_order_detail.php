<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>

<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Bảng điều khiển / Xem đơn hàng
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title" style="color: #d53d3d; font-size: 16px">
                    <?php
                    if (isset($_GET['view_order_detail'])) {
                        $invoice_no = $_GET['view_order_detail'];
                        echo "<i class='fa fa-tags'></i> Chi tiết đơn hàng: <span style='color: black'>$invoice_no</span>";
                    }
                    ?>
                </h3>
            </div>
            
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Địa Chỉ Email</th>
                                <th>Tên Sản phẩm</th>
                                <th>Số Lượng</th>
                                <th>Kích Cỡ</th>
                                <th>Ngày</th>
                                <th>Tổng</th>
                                <th>Xoá</th>
                                <th>Trạng Thái</th>
                                <th>Xác nhận trạng thái đơn</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['view_order_detail'])) {
                                $view_order_detail = $_GET['view_order_detail'];

                                $i = 0;
                                $total_amount = 0; // Khởi tạo tổng tiền

                                $get_orders = "SELECT * FROM customer_orders WHERE invoice_no = '$view_order_detail'";
                                $run_orders = mysqli_query($conn, $get_orders);

                                while ($row_order = mysqli_fetch_array($run_orders)) {
                                    $order_id = $row_order['order_id'];
                                    $customer_id = $row_order['customer_id'];
                                    $order_amount = number_format((float)$row_order['due_amount']);
                                    $invoice_no = $row_order['invoice_no'];
                                    $product_id = $row_order['product_id'];
                                    $product_size = $row_order['product_size'];
                                    $product_quantity = $row_order['product_quantity'];
                                    $order_date = $row_order['order_date'];
                                    $order_status = $row_order['order_status'];

                                    $get_products = "SELECT * FROM products WHERE product_id='$product_id'";
                                    $run_products = mysqli_query($conn, $get_products);
                                    $row_products = mysqli_fetch_array($run_products);
                                    $product_title = $row_products['product_title'];
                                    $product_price = $row_products['product_price']; // Lấy giá sản phẩm

                                    $get_customer = "SELECT * FROM customers WHERE customer_id='$customer_id'";
                                    $run_customer = mysqli_query($conn, $get_customer);
                                    $row_customer = mysqli_fetch_array($run_customer);
                                    $customer_email = $row_customer['customer_email'];

                                    $i++;
                                    
                                    // Tính tổng tiền cho từng sản phẩm và cộng dồn vào $total_amount
                                    $item_total = $product_price * $product_quantity;
                                    $total_amount += $item_total;
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $customer_email; ?></td>
                                        <td><?php echo $product_title; ?></td>
                                        <td><?php echo $product_quantity; ?></td>
                                        <td><?php echo $product_size; ?></td>
                                        <td><?php echo $order_date; ?></td>
                                        <td><?php echo number_format((float)$item_total); ?>₫</td> 
                                        <td>
                                            <?php if ($order_status != 'Completed'): ?>
                                                <span class="wrapperDelete">
                                                    <a class="tableDelete" href="index.php?delete_order=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>">
                                                        <i class="fa fa-trash-o"></i>
                                                        <div class="tooltip">Không thể khôi phục khi nhấn</div>
                                                    </a>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: gray">Hoàn thành</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="color: blue;">
                                            <?php
                                            if ($order_status == 'Pending') {
                                                echo "<span style='color:#000000; font-size: 18px'>Chờ xác nhận</span>";
                                            } else if ($order_status == 'Delivering') {
                                                echo "<span style='color:#cb456d; font-size: 18px'>Đang giao</span>";
                                            } else if ($order_status == 'Delivered') {
                                                echo "<span style='color:#00cc07; font-size: 18px'>Đã giao</span>";
                                            } else if ($order_status == 'Cancelled') {
                                                echo "<span style='color:#ff0000; font-size: 18px'>Đã hủy</span>";
                                            } else if ($order_status == 'Completed'){
                                                echo "<span style='color:#00008b; font-size: 18px'>Hoàn thành</span>";
                                            } else {
                                                echo "<span style='color:#0088cc; font-size: 18px'>Đã xác nhận</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="index.php?confirm_yes=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>" class="btn btn-primary btn-sm btn-confim">Xác nhận</a>
                                            <a href="index.php?confirm_delivering=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>" style="background: #cb456d;" class="btn btn-primary btn-sm btn-confim">Đang giao</a>
                                            <a href="index.php?confirm_delivered=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>" style="background: #00cc07;" class="btn btn-primary btn-sm btn-confim">Đã giao</a>
                                            <a href="index.php?confirm_cancelled=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>" style="background: #ff0000;" class="btn btn-primary btn-sm btn-confim">Hủy đơn hàng</a>
                                            <a href="index.php?confirm_completed=<?php echo $order_id; ?>&invoice_no=<?php echo $invoice_no; ?>" style="background: #00008b;" class="btn btn-primary btn-sm btn-confim">Hoàn thành</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    $total_amount = number_format((float)$total_amount);
                    echo "<h4 style='color: #48be50'>Tổng tiền đơn hàng là: $total_amount VNĐ</h4>";
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>