<?php
    session_start();

    if (isset($_SESSION['token'])) header("Location: map_2.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['user'];
        $password = $_POST['pass'];

        // 6a1446b4c22eee2d3adf03af34d7993f
        if ($username == 'david' && $password == md5('medc')) {
            $_SESSION['token'] = $username;
            header("Location: map_2.php");
        }

    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./descarga.png"/>
    <title>Document</title>
    <style>
        * { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            margin: 0; 
            padding: 0; 
            background: url(https://wallpaperaccess.com/full/1772833.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            background-blend-mode: multiply;
            background-color: rgba(0,0,0,.6);
        }
        .log {
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40%;
            min-height: 400px;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
            border-radius: 20px;
            transform: translate(-50%, -50%);
            background-color: white;
        }
        .log .m {
            position: absolute;
            bottom: 50px;
            left: 50%;
            width: 50%;
            transform: translate(-50%, 0);
        }
        .t-spc {
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
        }
        .frrm input {
            box-sizing: border-box;
            padding: 12px 10px;
            margin: 14px 0;
            width: 100%;
        }
        .w-100 {
            width: 100%;
        }
        .frrm {
            width: 50%;
        }

        .inpt {
            width: 100%;
            background-color: -internal-light-dark(rgb(239, 239, 239), rgb(59, 59, 59));
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .log {
                width: 85%;
            }
            .log .m {
                position: absolute;
                bottom: 0;
                width: 100%;
                text-align: center;
                left: 0;
                margin: 0;
                border-radius: 0 0 20px 20px;
                outline:none;
                border:none;
                height: 80px;
                font-size: 20px;
                /* color: white; */
                text-transform: uppercase;
                letter-spacing: 2px;
                /* background: grey; */
                transform: unset;
            }
            .center { padding-top: 50px; }
            .frrm{ width: 80%; }
        }
    </style>
</head>
<body>

    <div class="log">
        <div class="center">
            <h3 class="t-spc">LogIn</h3>

            <div class="frrm">
                <form method="post">
                    <div class="w-100">
                        <input type="text" class="inpt" name="user" placeholder="Usuario" required>
                    </div>
                    <div class="w-100">
                        <input type="password" class="inpt" name="pass" placeholder="Contraseña" required>
                    </div>
                    
                    <input type="submit" value="Entrar" class="m t-spc">
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>