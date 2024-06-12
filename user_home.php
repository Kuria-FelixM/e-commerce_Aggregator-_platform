<?php
  require_once 'connect.php';

  session_start();

  // Check if the user is logged in, if not then redirect them to the login page
  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Dashboard</title>

    <link rel="stylesheet" href="css/user_style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="title">USER</span>
                    </a>
                </li>

                <li>
                    <a href="user_home.php">
                        <span class="icon">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="user_home_explore.php">
                        <span class="icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <span class="title">SEARCH</span>
                    </a>
                </li>

                <li>
                    <a href="user_compare.php">
                        <span class="icon">
                            <i class="fa-solid fa-code-compare"></i>
                        </span>
                        <span class="title">Compare</span>
                    </a>
                </li>

                <li>
                    <a href="user_recommendations.php">
                        <span class="icon">
                            <i class="fa-solid fa-car"></i>
                        </span>
                        <span class="title">RECOMMENDED</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <span class="title">Settings</span>
                    </a>
                </li>


                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <p>WELCOME <?php echo $_SESSION["username"] ?></p>


            </div>

            <div class="main-content">
               <?php include 'user_index.php'; ?>
                
            
    

            </div>


            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/main.js"></script>

    
</body>

</html>