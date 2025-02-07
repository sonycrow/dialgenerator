<?php
ob_start();
header("Content-type: text/html");
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Styles -->
    <style>
        div.dial_image { margin-top: 20px; }
        div.card_image { margin-top: 40px; }
        textarea { min-height: 250px; }
    </style>

    <!-- Tittle -->
    <title>Heroclix Dial Generator</title>

</head>
<body>
    <div class="container">

        <div class="row">
            <div class="col">
                <h1>Heroclix Dial Generator</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <form>
                    <div class="form-group">
                        <label for="bbcode">Dial BBCODE from <b>hcrealms.com</b></label>
                        <textarea class="form-control" name="bbcode" id="bbcode" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="bsubmit">Generate</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row dial_image">
            <div class="col">
                <img src="" class="img-fluid" id="dial_image"/>
            </div>
        </div>

        <div class="row card_image">
            <div class="col">
                <img src="" class="img-fluid" id="card_image"/>
            </div>
        </div>

    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- Javascript -->
    <script>
        $("#bsubmit").click(function() {
            if ($("#bbcode").val().trim() != "") {
                $("#dial_image").attr("src", "/dial.php?token=" + window.btoa($("#bbcode").val().trim()));
                $("#card_image").attr("src", "/card.php?token=" + window.btoa($("#bbcode").val().trim()));
            }
        });
    </script>

</body>
</html>