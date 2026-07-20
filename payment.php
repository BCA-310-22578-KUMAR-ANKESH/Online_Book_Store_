<?php session_start();
include_once('includes/config.php');
if(strlen($_SESSION['id'])==0)
{   header('location:logout.php');
}else{
if($_SESSION['address']==0):
    echo "<script type='text/javascript'> document.location ='checkout.php'; </script>";
endif;    

// Check if returning from Mock Payment Gateway
if(isset($_GET['gateway_success']) && $_GET['gateway_success'] == 'true' && isset($_GET['txnnumber'])) {
    $txnno = mysqli_real_escape_string($con, $_GET['txnnumber']);
    $txntype = $_SESSION['paymenttype'];
    $orderno = mt_rand(100000000,999999999);
    $userid = $_SESSION['id'];
    $address = $_SESSION['address'];
    $totalamount = $_SESSION['gtotal'];
    
    $query = mysqli_query($con, "insert into orders(orderNumber,userId,addressId,totalAmount,txnType,txnNumber) values('$orderno','$userid','$address','$totalamount','$txntype','$txnno')");
    if($query) {
        $sql = "insert into ordersdetails (userId,productId,quantity) select userID,productId,productQty from cart where userID='$userid';";
        $sql .= "update ordersdetails set orderNumber='$orderno' where userId='$userid' and orderNumber is null;";
        $sql .= "delete from cart where userID='$userid'";
        
        // Execute multi_query and consume all results to prevent sync errors
        if (mysqli_multi_query($con, $sql)) {
            do {
                if ($result = mysqli_store_result($con)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($con));
        }
        
        unset($_SESSION['address']);
        unset($_SESSION['gtotal']);
        unset($_SESSION['paymenttype']);
        
        echo '<script>alert("Your order has been successfully placed. Order number is '.$orderno.'")</script>';
        echo "<script type='text/javascript'> document.location ='my-orders.php'; </script>";
        exit();
    } else {
        echo "<script>alert('Something went wrong. Please try again');</script>";
        echo "<script type='text/javascript'> document.location ='payment.php'; </script>";
        exit();
    }
}

//Order details
if(isset($_POST['submit']))
{
    $userid=$_SESSION['id'];
    $address=$_SESSION['address'];
    $totalamount=$_SESSION['gtotal'];
    $txntype=$_POST['paymenttype'];

    if ($txntype == 'Cash on Delivery') {
        $orderno= mt_rand(100000000,999999999);
        $txnno = 'COD';
        $query=mysqli_query($con,"insert into orders(orderNumber,userId,addressId,totalAmount,txnType,txnNumber) values('$orderno','$userid','$address','$totalamount','$txntype','$txnno')");
        if($query)
        {
            $sql="insert into ordersdetails (userId,productId,quantity) select userID,productId,productQty from cart where userID='$userid';";
            $sql.="update ordersdetails set orderNumber='$orderno' where userId='$userid' and orderNumber is null;";
            $sql.="delete from  cart where userID='$userid'";
            
            if (mysqli_multi_query($con, $sql)) {
                do {
                    if ($result = mysqli_store_result($con)) {
                        mysqli_free_result($result);
                    }
                } while (mysqli_next_result($con));
            }
            
            unset($_SESSION['address']);
            unset($_SESSION['gtotal']);    
            echo '<script>alert("Your order successfully placed. Order number is "+"'.$orderno.'")</script>';
            echo "<script type='text/javascript'> document.location ='my-orders.php'; </script>";
            exit();
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
            echo "<script type='text/javascript'> document.location ='payment.php'; </script>";
            exit();
        }
    } else {
        // Redirect to Mock Gateway
        $_SESSION['paymenttype'] = $txntype;
        echo "<script type='text/javascript'> document.location ='mock-payment-gateway.php'; </script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Online Book Store||Payment</title>
	
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header -->
<?php include_once('includes/header.php');?>
	<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
		<h2 class="ltext-105 cl0 txt-center">
			Payment
		</h2>
	</section>
	
<hr>
		

		<section class="bg0 p-t-104 p-b-116">
		<div class="container">
			<div class="flex-w flex-tr">
				<div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
					<form method="post" name="login">
						<h4 class="mtext-105 cl2 txt-center p-b-30">
							Payment Details
						</h4>

						<div  class="bor8 m-b-20 how-pos4-parent">
							<label>Total Payment</label>
							<input type="text" name="totalamount" value="<?php echo  $_SESSION['gtotal'];?>" class="form-control" readonly >
						</div>
						<div  class="bor8 m-b-20 how-pos4-parent">
							<label>Payment Type</label>
							 <select class="form-control" name="paymenttype" id="paymenttype" required>
                <option value="">Select</option>
                <option value="UPI">UPI</option>
                <option value="Internet Banking">Internet Banking</option>
                <option value="Debit/Credit Card">Debit/Credit Card</option>
                <option value="Cash on Delivery">Cash on Delivery (COD)</option>
            </select>
							
						</div>
						<button type="submit" name="submit" id="submit" class="btn btn-primary" style="width: 100%; border-radius: 4px; padding: 12px; font-weight: bold;" disabled>Please Select Payment Type</button>
					</form>
				</div>

				
			</div>
		</div>
	</section>	




	
		

	<?php include_once('includes/footer.php'); ?>


<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/slick/slick.min.js"></script>
	<script src="js/slick-custom.js"></script>
<!--===============================================================================================-->
	<script src="vendor/parallax100/parallax100.js"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
<!--===============================================================================================-->
	<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/sweetalert/sweetalert.min.js"></script>
	<script>
		$('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

		$('.js-addcart-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to cart !", "success");
			});
		});
	
	</script>
<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
	<script type="text/javascript">

  $(document).ready(function(){
    $('#paymenttype').change(function(){
      var ptype = $(this).val();
      var submitBtn = $('#submit');
      if(ptype == 'Cash on Delivery') {
        submitBtn.text('Place Order (Cash on Delivery)');
        submitBtn.prop('disabled', false);
      } else if(ptype == '') {
        submitBtn.text('Please Select Payment Type');
        submitBtn.prop('disabled', true);
      } else {
        submitBtn.text('Proceed to Pay via Secure Gateway (' + ptype + ')');
        submitBtn.prop('disabled', false);
      }
    });
  }); 
</script>

</body>
</html><?php } ?> 