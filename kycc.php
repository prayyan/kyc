<?php
require_once('../admin/dbcon.php');

session_start();
if (isset($_SESSION['userInfo'])) {
  // Access the userInfo
  $userInfo = $_SESSION['userInfo'];

  // Rest of your code for KYC verification form processing
} else {
  // Redirect the user to the login page or handle the case when the user is not logged in
  header("Location: loginSignUp.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addressProof = $_POST['addressProof'];
    $drivingLicenseNumber = $_POST['drivingLicenseNumber'];

    // Process and move the uploaded images
    $addressProofFront = '';
    if (isset($_FILES['addressProofFront']) && $_FILES['addressProofFront']['error'] === UPLOAD_ERR_OK) {
        $addressProofFront = $_FILES['addressProofFront']['tmp_name'];
    }

    $addressProofBack = '';
    if (isset($_FILES['addressProofBack']) && $_FILES['addressProofBack']['error'] === UPLOAD_ERR_OK) {
        $addressProofBack = $_FILES['addressProofBack']['tmp_name'];
    }

    $drivingLicenseFront = '';
    if (isset($_FILES['drivingLicenseFront']) && $_FILES['drivingLicenseFront']['error'] === UPLOAD_ERR_OK) {
        $drivingLicenseFront = $_FILES['drivingLicenseFront']['tmp_name'];
    }

    $drivingLicenseBack = '';
    if (isset($_FILES['drivingLicenseBack']) && $_FILES['drivingLicenseBack']['error'] === UPLOAD_ERR_OK) {
        $drivingLicenseBack = $_FILES['drivingLicenseBack']['tmp_name'];
    }

    // Update the image data in the database
    $updateStmt = mysqli_prepare($connection, "UPDATE users SET addressProof = ?, addressProofFront = ?, addressProofBack = ?, drivingLicenseNumber = ?, drivingLicenseFront = ?, drivingLicenseBack = ? WHERE user = ? OR email = ?");

    if ($updateStmt) {
        // Bind parameters with their actual values
        mysqli_stmt_bind_param($updateStmt, "sbbsbbss", $addressProof, $addressProofFront, $addressProofBack, $drivingLicenseNumber, $drivingLicenseFront, $drivingLicenseBack, $userInfo, $userInfo);

        // Send long data for image columns
        mysqli_stmt_send_long_data($updateStmt, 1, file_get_contents($addressProofFront));
        mysqli_stmt_send_long_data($updateStmt, 2, file_get_contents($addressProofBack));
        mysqli_stmt_send_long_data($updateStmt, 4, file_get_contents($drivingLicenseFront));
        mysqli_stmt_send_long_data($updateStmt, 5, file_get_contents($drivingLicenseBack));

        // Execute the statement
        if (mysqli_stmt_execute($updateStmt)) {
            // Update successful
            echo "Data updated in the datatable successfully.";
        } else {
            // Update failed
            echo "Failed to update data in the datatable: " . mysqli_stmt_error($updateStmt);
        }

        // Close the statement
        mysqli_stmt_close($updateStmt);
    } else {
        // Statement preparation failed
        echo "Failed to prepare statement: " . mysqli_error($connection);
    }
}
?>


<!Doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="https://res.cloudinary.com/dpdwa1atx/image/upload/v1686602059/Prayyan%20Website/2_ajsout.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/libs/fontawesome/css/fontawesome-all.min.css">
	<link rel="stylesheet" href="assets/libs/linearicons/linearicons.css">
	<link rel="stylesheet" href="assets/css/rentnow-icons.css">
	<link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<link rel="stylesheet" href="assets/css/style.css">
  </head>
  <style>
    .kyc{
      margin-top: 200px;
      padding-top: 100px;
    }
    </style>
  
  <body>
    <?php
		  include 'header.php'; // Replace 'header.php' with the actual path to your header file
		?>
    <section style="background-color: #151414;">
        <div class="container-fluid h-100 mt-5 kyc">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
              <div class="card text-black" style="border-radius: 25px;">
                <div class="card-body p-md-5">
                  <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                      <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4"><b>KYC Verification</b></p>
                      <form class="mx-1 mx-md-4" action="kycc.php" method="post" enctype="multipart/form-data">
                        <div class="d-flex flex-row align-items-center">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                                <label for="formFile" class="form-label"><b>Address Proof Type:</b></label>
                                <select name="addressProof" required>
                                    <option value="">Select Address Proof Type</option>
                                    <option value="Aadhaar">Aadhaar</option>
                                    <option value="Passport">Passport</option>
                                </select>
                                <div class="mb-3 mt-2"> 
                                    <label for="formFile" class="form-label"><b>Adress Proof Front Page</b></label>
                                    <input class="form-control" type="file" name="addressProofFront" required>
                                    <div class="small text-muted mt-1">Upload your Selected file. Max file
                                        size 120 KB
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="formFile" class="form-label"><b>Adress Proof Back Page</b></label>
                                    <input class="form-control" type="file" name="addressProofBack" required>
                                    <div class="small text-muted mt-1">Upload your Selected file. Max file
                                        size 120 KB
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                          <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                          <div class="form-outline flex-fill mb-3">
                            <label class="form-label" for="form3Example3c"><b>Driving License Number</b></label>
                            <input type="text" name="drivingLicenseNumber" class="form-control" required>
                          </div>
                        </div>
                        <div class="d-flex flex-row align-items-center mb-3">
                            <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                                <div class="mb-3 mt-2">
                                    <label for="formFile" class="form-label"><b>Driving License Front Page</b></label>
                                    <input class="form-control" type="file" name="drivingLicenseFront" required>
                                    <div class="small text-muted mt-1">Upload your Selected file. Max file
                                        size 120 KB
                                    </div>
                                </div>
                                <div class="mb-3 mt-2">
                                    <label for="formFile" class="form-label"><b>Driving License Back Page</b></label>
                                    <input class="form-control" type="file" name="drivingLicenseBack" required>
                                    <div class="small text-muted mt-1">Upload your Selected file. Max file
                                        size 120 KB
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mb-4 mb-lg-4 ">
                            <button type="submit" class="btn btn-primary btn-lg" >Verify</button>
                        </div>
                      </form>
                    </div>
                    <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
      
                      <img src="https://res.cloudinary.com/dpdwa1atx/image/upload/v1685509263/Prayyan%20Website/visual-collaboration_ek5cni.png"
                        class="img-fluid" alt="Sample image">
      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
        <?php include 'footer.php'; ?>
        <script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
		<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
		<script src="assets/js/starrr.min.js"></script>
		<script src="assets/js/jquery.magnific-popup.min.js"></script>
		<script src="assets/js/scripts.js"></script>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-3.6.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  </body>
</html>