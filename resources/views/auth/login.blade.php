<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <title>SIGEP - Login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">


    <style>


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }



        body {

            height: 100vh;

            background: url('/images/bg-login.jpg') no-repeat center center/cover;

            display: flex;

            align-items: center;

            justify-content: center;

        }



        /* Overlay verde */

        body::before {

            content: "";

            position: absolute;

            width: 100%;

            height: 100%;

            background: rgba(0, 128, 96, 0.85);

            backdrop-filter: blur(3px);

        }




        .login-container {

            position: relative;

            width: 380px;

            background: #f2f2f2;

            border-radius: 15px;

            padding: 30px;

            text-align: center;

            box-shadow: 0 15px 40px rgba(0,0,0,0.3);

            z-index: 1;

        }





        .logo {

            width: 120px;

            margin-bottom: 10px;

        }





        h1 {

            font-size: 22px;

            margin-bottom: 5px;

            font-weight: 600;

        }




        h2 {

            font-size: 16px;

            margin-bottom: 20px;

            color: #555;

        }





        .input-group {

            margin-bottom: 15px;

        }





        input {

            width: 100%;

            padding: 12px;

            border-radius: 8px;

            border: 1px solid #ccc;

            outline: none;

            transition: 0.3s;

            font-size: 14px;

        }





        input:focus {

            border-color: #2e8b57;

            box-shadow: 0 0 5px rgba(46,139,87,0.4);

        }





        .btn {

            width: 100%;

            padding: 12px;

            border-radius: 8px;

            border: none;

            cursor: pointer;

            font-weight: 600;

            margin-top: 10px;

            transition: 0.3s;

            display: block;

            text-decoration: none;

            text-align: center;

            font-size: 14px;

        }





        .btn-login {

            background: #2e8b57;

            color: white;

        }





        .btn-login:hover {

            background: #256f47;

        }





        .btn-register {

            background: #bdbdbd;

            color: white;

        }





        .btn-register:hover {

            background: #9e9e9e;

        }





        .forgot {

            margin-top: 20px;

            font-size: 12px;

        }





        .forgot a {

            color: #2e8b57;

            text-decoration: none;

            font-size: 12px;

            font-weight: 500;

        }





        .forgot a:visited {

            color: #2e8b57;

        }





        .forgot a:hover {

            text-decoration: underline;

        }





        @media (max-width: 420px) {


            .login-container {

                width: 90%;

                padding: 20px;

            }


        }



    </style>


</head>




<body>



<div class="login-container">



    <!-- LOGO -->

    <img src="/images/logo-ifpr.png" class="logo">




    <h1>SIGEP</h1>

    <h2>PLENÁRIO DIGITAL</h2>




    <form method="POST" action="/login">

        @csrf



        <div class="input-group">

            <input type="text" name="suap" placeholder="Suap" required>

        </div>




        <div class="input-group">

            <input type="password" name="password" placeholder="Senha" required>

        </div>




        <button type="submit" class="btn btn-login">

            ENTRAR

        </button>



    </form>





    <a href="{{ route('register') }}" class="btn btn-register">

        CADASTRAR

    </a>





    <div class="forgot">

        <a href="{{ route('forgot-password') }}">

            Esqueci minha senha

        </a>

    </div>




</div>



</body>


</html>