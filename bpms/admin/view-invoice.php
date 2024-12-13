<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['bpmsaid']==0)) {
  header('location:logout.php');
} else {
?>
<!DOCTYPE HTML>
<html>
<head>
<title>BPMS || View Invoice</title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- font CSS -->
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
 <!-- js-->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<!--//webfonts--> 
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
<script>
     new WOW().init();
</script>
<!--//end-animate-->
<!-- Metis Menu -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
<!--//Metis Menu -->
</head> 
<body class="cbp-spmenu-push">
    <div class="main-content">
        <!--left-fixed -navigation-->
        <?php include_once('includes/sidebar.php');?>
        <!--left-fixed -navigation-->
        <!-- header-starts -->
        <?php include_once('includes/header.php');?>
        <!-- //header-ends -->
        <!-- main content start-->
        <div id="page-wrapper">
            <div class="main-page">
                <div class="tables" id="exampl">
                    <h3 class="title1">Invoice Details</h3>
                    
                    <?php
                    $invid=intval($_GET['invoiceid']);
                    $ret=mysqli_query($con,"select DISTINCT date(tblinvoice.PostingDate) as invoicedate,tbluser.FirstName,tbluser.LastName,tbluser.Email,tbluser.MobileNumber,tbluser.RegDate
                        from  tblinvoice 
                        join tbluser on tbluser.ID=tblinvoice.Userid 
                        where tblinvoice.BillingId='$invid'");
                    $cnt=1;
                    while ($row=mysqli_fetch_array($ret)) {
                    ?>              
                    
                    <div class="table-responsive bs-example widget-shadow">
                        <h4>Invoice #<?php echo $invid;?></h4>
                        <table class="table table-bordered" width="100%" border="1"> 
                            <tr>
                                <th colspan="6">Customer Details</th>   
                            </tr>
                            <tr> 
                                <th>Name</th> 
                                <td><?php echo $row['FirstName']?> <?php echo $row['LastName']?></td> 
                                <th>Contact no.</th> 
                                <td><?php echo $row['MobileNumber']?></td>
                                <th>Email </th> 
                                <td><?php echo $row['Email']?></td>
                            </tr> 
                            <tr> 
                                <th>Registration Date</th> 
                                <td><?php echo $row['RegDate']?></td> 
                                <th>Invoice Date</th> 
                                <td colspan="3"><?php echo $row['invoicedate']?></td> 
                            </tr> 
                            <?php }?>
                        </table> 
                        <table class="table table-bordered" width="100%" border="1"> 
                            <tr>
                                <th colspan="3">Services Details</th>   
                            </tr>
                            <tr>
                                <th>#</th>  
                                <th>Service</th>
                                <th>Cost</th>
                            </tr>

                            <?php
                            $ret=mysqli_query($con,"select tblservices.ServiceName,tblservices.Cost  
                                from  tblinvoice 
                                join tblservices on tblservices.ID=tblinvoice.ServiceId 
                                where tblinvoice.BillingId='$invid'");
                            $cnt=1;
                            $gtotal = 0;
                            while ($row=mysqli_fetch_array($ret)) {
                            ?>
                            <tr>
                                <th><?php echo $cnt;?></th>
                                <td><?php echo $row['ServiceName']?></td>   
                                <td><?php echo $subtotal=$row['Cost']?></td>
                            </tr>
                            <?php 
                            $cnt=$cnt+1;
                            $gtotal+=$subtotal;
                            } ?>

                            <tr>
                                <th colspan="2" style="text-align:center">Grand Total</th>
                                <th><?php echo $gtotal?></th>   
                            </tr>

                            <?php
                            // Fetch payment status
                            $ret_payment = mysqli_query($con, "SELECT payment_status, payment_option_id FROM tblinvoice WHERE BillingId='$invid' LIMIT 1");
                            $payment_row = mysqli_fetch_array($ret_payment);
                            $payment_status = $payment_row['payment_status'] ?? 'unpaid';
                            $current_payment_option = $payment_row['payment_option_id'];

                            // Fetch payment options
                            $ret_payment_options = mysqli_query($con, "SELECT payment_ID, payment_name FROM payment_option");
                            ?>

                            <tr>
                                <th colspan="2" style="text-align:center">Payment Option</th>
                                <td>
                                    <select name="payment_option" id="payment_option" class="form-control" <?php echo $payment_status === 'paid' ? 'disabled' : ''; ?>>
                                        <option value="">Select Payment Method</option>
                                        <?php 
                                        while($row_payment = mysqli_fetch_array($ret_payment_options)) {
                                            $selected = ($row_payment['payment_ID'] == $current_payment_option) ? 'selected' : '';
                                            echo '<option value="'.$row_payment['payment_ID'].'" '.$selected.'>'.$row_payment['payment_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2" style="text-align:center">Payment Status</th>
                                <td>
                                    <select name="payment_status" id="payment_status" class="form-control" onchange="updatePaymentStatus(this.value, '<?php echo $invid; ?>')" <?php echo $payment_status === 'paid' ? 'disabled' : ''; ?>>
                                        <option value="unpaid" <?php echo $payment_status === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                        <option value="paid" <?php echo $payment_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <p style="margin-top:1%" align="center">
                            <i class="fa fa-print fa-2x" style="cursor: pointer;" OnClick="CallPrint(this.value)"></i>
                        </p>
						<script>
 function CallPrint() {
    var prtContent = document.getElementById("exampl");
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    
    // Adding custom print styles
    WinPrint.document.write('<html><head><title>Invoice Print</title>');
    WinPrint.document.write('<style>');
    WinPrint.document.write('body { font-family: "Courier New", Courier, monospace; font-size: 12px; margin: 20px;}');
    WinPrint.document.write('table { width: 100%; border-collapse: collapse; }');
    WinPrint.document.write('th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }');
    WinPrint.document.write('th { background-color: #f2f2f2; font-weight: bold; }');
    WinPrint.document.write('td { font-size: 14px; }');
    WinPrint.document.write('h4 { text-align: center; font-size: 18px; margin-top: 10px; }');
    WinPrint.document.write('.grand-total { font-weight: bold; text-align: center; font-size: 16px; margin-top: 20px; }');
    WinPrint.document.write('.receipt-header { text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 20px; }');
    WinPrint.document.write('.receipt-footer { text-align: center; font-size: 12px; margin-top: 20px; }');
    WinPrint.document.write('</style></head><body>');
    
    // Adding header for receipt
    WinPrint.document.write('<div class="receipt-header">Receipt</div>');
    
    // Write the content of the invoice into the print window
    WinPrint.document.write(prtContent.innerHTML);
    
    // Adding footer for receipt
    WinPrint.document.write('<div class="receipt-footer">Thank you for your business!</div>');
    
    WinPrint.document.write('</body></html>');
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
}
</script>
                    </div>
                </div>
            </div>
        </div>
        <!--footer-->
        <?php include_once('includes/footer.php');?>
        <!--//footer-->
    </div>
    <!-- Classie -->
    <script src="js/classie.js"></script>
    <script>
        var menuLeft = document.getElementById('cbp-spmenu-s1'),
            showLeftPush = document.getElementById('showLeftPush'),
            body = document.body;
            
        showLeftPush.onclick = function() {
            classie.toggle(this, 'active');
            classie.toggle(body, 'cbp-spmenu-push-toright');
            classie.toggle(menuLeft, 'cbp-spmenu-open');
            disableOther('showLeftPush');
        };
        
        function disableOther(button) {
            if(button !== 'showLeftPush') {
                classie.toggle(showLeftPush, 'disabled');
            }
        }
        function updatePaymentStatus(status, invoiceId) {
    if (status === 'paid') {
        if (confirm('Are you sure you want to mark this invoice as paid? This action cannot be undone.')) {
            const paymentOption = document.getElementById('payment_option').value;
            if (!paymentOption) {
                alert('Please select a payment method first');
                document.getElementById('payment_status').value = 'unpaid';
                return;
            }

            $.ajax({
                url: 'update_payment_status.php',
                type: 'POST',
                data: {
                    invoice_id: invoiceId,
                    payment_status: status,
                    payment_option: paymentOption
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            document.getElementById('payment_status').disabled = true;
                            document.getElementById('payment_option').disabled = true;
                            alert('Payment status updated successfully');

                            // Call the CallPrint function to print the invoice
                            CallPrint(); // <--- Add this line
                        } else {
                            alert('Error updating payment status');
                            document.getElementById('payment_status').value = 'unpaid';
                        }
                    } catch(e) {
                        alert('Error processing response');
                        document.getElementById('payment_status').value = 'unpaid';
                    }
                },
                error: function() {
                    alert('Error updating payment status');
                    document.getElementById('payment_status').value = 'unpaid';
                }
            });
        } else {
            document.getElementById('payment_status').value = 'unpaid';
        }
    }
}
    </script>
    <!--scrolling js-->
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/scripts.js"></script>
    <!--//scrolling js-->
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.js"></script>
</body>
</html>
<?php } ?>