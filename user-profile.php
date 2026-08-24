.<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['detsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$msg = "";

if (isset($_POST['submit'])) {
    $userid = $_SESSION['detsuid'];
    $fullname = $_POST['fullname'];
    $mobno = $_POST['contactnumber'];

    if (!empty($_FILES['image']['name'])) {
        // Get old image
        $getOldImage = mysqli_query($con, "SELECT imagename FROM tbluser WHERE ID='$userid'");
        $row = mysqli_fetch_assoc($getOldImage);
        $oldImage = $row['imagename'];

        // Delete old image if exists
        if (!empty($oldImage) && file_exists("uploads/$oldImage")) {
            unlink("uploads/$oldImage");
        }

        $image = uniqid() . "_" . basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $query = mysqli_query($con, "UPDATE tbluser SET FullName='$fullname', MobileNumber='$mobno', imagename='$image' WHERE ID='$userid'");
        } else {
            $msg = "Failed to upload image.";
        }
    } else {
        // If no new image uploaded, only update name and number
        $query = mysqli_query($con, "UPDATE tbluser SET FullName='$fullname', MobileNumber='$mobno' WHERE ID='$userid'");
    }

    if ($query) {
        $msg = "User profile has been updated.";
    } else {
        $msg = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker || User Profile</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    
    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
</head>
<body>
    <?php include_once('includes/header.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><em class="fa fa-home"></em></a></li>
                <li class="active">Profile</li>
            </ol>
        </div><!--/.row-->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Profile</div>
                    <div class="panel-body">
                        <p style="font-size:16px; color:red" align="center">
                            <?php if ($msg) { echo $msg; } ?>
                        </p>
                        <div class="col-md-12">
                            <?php
                            $userid = $_SESSION['detsuid'];
                            $ret = mysqli_query($con, "SELECT * FROM tbluser WHERE ID='$userid'");
                            $row = mysqli_fetch_array($ret);
                            ?>
                            <form role="form" method="post" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="form-control" type="text" value="<?php echo $row['FullName']; ?>" name="fullname" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo $row['Email']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input class="form-control" type="text" value="<?php echo $row['MobileNumber']; ?>" name="contactnumber" maxlength="10" required>
                                </div>
                                <div class="form-group">
                                    <label>Registration Date</label>
                                    <input class="form-control" name="regdate" type="text" value="<?php echo $row['RegDate']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Image</label><br>
                                    <?php if ($row['imagename']) { ?>
                                        <img src="uploads/<?php echo $row['imagename']; ?>" width="100" height="100">
                                    <?php } else { ?>
                                        <p>No image uploaded</p>
                                    <?php } ?>
                                    <input type="file" name="image" class="form-control">
                                </div>
                                <div class="form-group has-success">
                                    <button type="submit" class="btn btn-primary" name="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /.panel-->
            </div><!-- /.col-->
            <?php include_once('includes/footer.php'); ?>
        </div><!-- /.row -->
    </div><!--/.main-->

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/chart-data.js"></script>
    <script src="js/easypiechart.js"></script>
    <script src="js/easypiechart-data.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
