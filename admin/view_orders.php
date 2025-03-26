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
                <h3 class="panel-title">
                    <i class="fa fa-tags"></i> Xem tất cả đơn hàng
                </h3>
            </div>
            
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Địa Chỉ Email</th>
                                <th>Số Hoá Đơn</th>
                                <th>Ngày</th>
                                <th>Tổng</th>
                                <th>Trạng Thái</th>
                                <th>Xem đơn hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;

                            // Truy vấn tất cả đơn hàng và sắp xếp theo ngày giảm dần (mới nhất trước)
                            $get_orders = "SELECT 
                                                co.invoice_no, 
                                                co.order_id, 
                                                co.customer_id, 
                                                co.due_amount, 
                                                co.order_date, 
                                                co.order_status, 
                                                c.customer_email 
                                            FROM 
                                                customer_orders co 
                                            JOIN 
                                                customers c ON co.customer_id = c.customer_id 
                                            GROUP BY 
                                                co.invoice_no 
                                            ORDER BY 
                                                co.order_date DESC"; // Sắp xếp theo ngày giảm dần

                            $run_orders = mysqli_query($conn, $get_orders);

                            if (mysqli_num_rows($run_orders) == 0) {
                                echo "<tr><td colspan='7' class='text-center'>Không có đơn hàng nào.</td></tr>";
                            } else {
                                while ($row_order = mysqli_fetch_array($run_orders)) {
                                    $order_id = $row_order['order_id'];
                                    $customer_email = $row_order['customer_email'];
                                    $invoice_no = $row_order['invoice_no'];
                                    $order_date = date("d/m/Y H:i:s", strtotime($row_order['order_date'])); // Định dạng ngày tháng
                                    $order_amount = number_format((float)$row_order['due_amount']);
                                    $order_status = $row_order['order_status'];

                                    $i++;
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $customer_email; ?></td>
                                        <td><a href="index.php?view_order_detail=<?php echo $invoice_no; ?>"><?php echo $invoice_no; ?></a></td>
                                        <td><?php echo $order_date; ?></td>
                                        <td><?php echo $order_amount; ?>₫</td>
                                        <td>
                                            <?php
                                            if ($order_status == 'Pending') {
                                                echo 'Chờ Xử Lý';
                                            } else if ($order_status == 'Delivering') {
                                                echo 'Đang giao';
                                            } else {
                                                echo 'Đã Thanh Toán';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="index.php?view_order_detail=<?php echo $invoice_no; ?>" class="btn btn-primary btn-sm btn-confim">Xem đơn hàng</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>