<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Twitch Announcements - Setup Successful!</title>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="cover.css" rel="stylesheet">
  </head>
  <body class="text-center">
        <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
            <header class="masthead mb-auto">
                <div class="inner">
                  <h3 class="masthead-brand">Setup Successful!</h3>
                  <nav class="nav nav-masthead justify-content-center">
                  </nav>
                </div>
            </header>

          <main role="main" class="inner cover">
            <div style="">
     
            <img src="https://www.freepnglogos.com/uploads/discord-logo-png/playerunknown-battlegrounds-bgparty-15.png">
            </div>
            <h1 class="cover-heading">Twitch Setup Successful!</h1>
            <h3 class="cover-heading">One last step!<h3>
            <p class="lead">Your twitch account will now notify Pinót's bot when you go live! All you have left is to tell the bot which text channel in discord to post to when you go live. <br> Your authentication code is: <code> <?php $code = $_REQUEST['code']; echo $code;?></code></p>
            <p class="lead">To finish setup type this command in your discord server: <br> <code>!twitchsetup #&lt;your-text-channel&gt; &lt;authentication-code&gt; </code></p>
            <p class="lead">For example if you have a text-channel named <code>#announcements</code>, your command would be: <br> <code>!twitchsetup #announcements <?php $code = $_REQUEST['code']; echo $code;?></code> </p>
            <p class="lead">
                Enter that command in your discord server and you're all set! <br> Enjoy and <b>@Pinót </b>if anything goes wrong or if you have questions! <br> Have a good stream! 
            </p>
          </main>

          <footer class="mastfoot mt-auto">
            <div class="inner">
            <!--
-->
            </div>
          </footer>

        </div>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
