<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - Project Approval</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="card mb-3">
                @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif

              <div class="d-flex justify-content-center py-4">
                <a href=" " class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">Project Approval</span>
                </a>
              </div><!-- End Logo -->

              

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form action="{{route('custome-register')}}" id="register-page" method="POST" class="row g-3">
                    @csrf
                       <div class="form-group">
                            <label class="form-label">Your Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                            <div class="error text-danger" id="name_error"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Your email</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}">
                            <div class="error text-danger" id="email_error"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Your password</label>
                            <input type="text" class="form-control" name="password" id="password">
                            <div class="error text-danger" id="password_error"></div>
                        </div>

                        <div class="col-12">
                          <button class="btn btn-primary w-100" type="submit">Create Account</button>
                        </div>
                        <div class="col-12">
                          <p class="small mb-0">Already have an account? <a href="{{ route('login-page') }}">Log in</a></p>
                        </div>                  
                  </form>
                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                {{-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> --}}
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {

      const form = document.getElementById("register-page");

      form.addEventListener("submit", function(e) {

          const name = document.getElementById("name").value.trim();
          const email = document.getElementById("email").value.trim();
          const password = document.getElementById("password").value.trim();
          const nameError = document.getElementById("name_error");
          const emailError = document.getElementById("email_error");
          const passwordError = document.getElementById("password_error");

          nameError.textContent = "";
          emailError.textContent = "";
          passwordError.textContent = "";

          let isValid = true;

          // NAME VALIDATION
          if (name === "") {
              nameError.textContent = "Name is required";
              isValid = false;
          } 
          else if (!/^[A-Za-z\s]+$/.test(name)) {
              nameError.textContent = "Name can only contain letters";
              isValid = false;
          }


          if (email === "") {
              emailError.textContent = "Email is required";
              isValid = false;
          } 

          
          if (password === "") {
              passwordError.textContent = "Password is required";
              isValid = false;
          }

          // Agar validation fail ho to form submit na ho
          if (!isValid) {
              e.preventDefault();
          }

      });

  });
</script>

</body>

</html>
