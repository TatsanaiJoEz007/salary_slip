<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once('language.php');

// if (session_status() == PHP_SESSION_NONE) {
//   session_start();
// }
?>


<style>
  @keyframes slideDown {
    from {
      transform: translateY(-100%);
    }
    to {
      transform: translateY(0);
    }
  }

  .navbar {
    background-color: #F0592E;
    padding: 10px 20px;
    position: relative;
    z-index: 1000;
    /* High z-index to stay on top */
  }

  .navbar .navbar-nav .nav-link {
    color: white !important;
    font-size: 16px;
    font-weight: bold;
  }

  .navbar .navbar-toggler {
    border: none;
  }

  .navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }

  /* Style for language switcher icons */
  .language-switcher {
    display: flex;
    align-items: center;
    margin-left: auto;
  }

  .language-switcher img {
    width: 30px;
    margin-left: 5px;
    cursor: pointer;
    margin-top: 5px;
  }

  /* New style for fixed navbar */
  .navbar.fixed-top {
    animation: slideDown 0.5s forwards;
    background-color: #F0592E;
  }

  .navbar-brand img {
    width: 90px;
    height: auto;
    transition: width 0.3s;
    position: absolute;
    top: -1.9px;
    left: 20px;
    z-index: 1001;
    /* Higher z-index than navbar */
  }

  .navbar-nav {
    margin-left: 150px;
  }

  /* Responsive adjustments */
  @media (max-width: 810px) {
    .navbar-brand img {
      width: 70px;
      top: 9px;
      left: 5px;
    }

    .language-switcher {
      margin-left: -8px;
      margin-top: 10px;
      justify-content: center;
      width: 100%;
      order: 1;
    }

    .navbar-nav {
      margin-left: 0;
      text-align: center;
    }

    .profile-dropdown {
      max-width: fit-content;
      margin-left: 367px;
      margin-right: auto;
    }
  }

  @media (max-width: 768px) {
    .navbar-brand img {
      width: 90px;
      top: 9px;
      left: 10px;
    }

    .navbar-nav {
      margin-left: 100px;
    }
  }

  @media (max-width: 576px) {
    .navbar-brand img {
      width: 70px;
      top: 9px;
      left: 5px;
    }

    .language-switcher {
      margin-left: 0;
      margin-top: 10px;
      justify-content: center;
      width: 100%;
      order: 1;
    }

    .navbar-nav {
      margin-left: 0;
      text-align: center;
    }

  }
</style>
</head>

<body>

  <nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-orange">
    <div class="container-fluid">
      <a class="navbar-brand" href="../view/index">
        <img src="../view/assets/img/logo/logo.png" alt="Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="../view/freqquestion_main"><?php echo $lang_question ?> ?</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../view/contact_main"><?php echo $lang_contact ?></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo $lang_login ?> / <?php echo $lang_register ?>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="../view/login"><?php echo $lang_login ?></a></li>
              <li><a class="dropdown-item" href="../view/register"><?php echo $lang_register ?></a></li>
            </ul>
          </li>
        </ul>
        <div class="language-switcher">
          <img src="../view/assets/img/logo/thai.png" alt="<?php echo $lang_th_language ?>" onclick="switchLanguage('th')">
          <img src="../view/assets/img/logo/ENG.png" alt="<?php echo $lang_en_language ?>" onclick="switchLanguage('en')">
        </div>
      </div>
    </div>
  </nav>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function switchLanguage(lang) {
      $.post('function/language.php', { lang: lang }, function(data) {
        if (data.success) {
          location.reload();
        }
      }, 'json');
    }

    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('navbar');
      const scrollY = window.scrollY;
      if (scrollY > 0) {
        navbar.classList.add('fixed-top');
      } else {
        navbar.classList.remove('fixed-top');
      }
    });
  </script>

</body>
