<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <title>SIGEP - Cadastro</title>

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



        body::before {

            content: "";

            position: absolute;

            width: 100%;

            height: 100%;

            background: rgba(0, 128, 96, 0.85);

            backdrop-filter: blur(3px);

        }



        .register-container {


            position: relative;

            width: 430px;

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

            color:#555;

        }



        h3 {

            font-size: 22px;

            margin-bottom: 25px;

        }



        .input-group {

            margin-bottom: 15px;

        }



        input {


            width: 100%;

            padding: 12px;

            border-radius: 8px;

            border: 1px solid #ccc;

            outline:none;

            font-size:14px;

            transition:.3s;

        }



        input:focus {

            border-color:#2e8b57;

            box-shadow:0 0 5px rgba(46,139,87,0.4);

        }



        .btn {


            width:100%;

            padding:12px;

            border-radius:8px;

            border:none;

            cursor:pointer;

            font-weight:600;

            margin-top:10px;

            transition:.3s;

            display:block;

            text-align:center;

            text-decoration:none;

            font-size:14px;

        }



        .btn-register {


            background:#2e8b57;

            color:white;

        }



        .btn-register:hover {


            background:#256f47;

        }



        .login-link {


            margin-top:15px;

            font-size:13px;

            color:#999;

        }



        .login-link a {


            color:#2e8b57;

            text-decoration:none;

            font-weight:600;

        }



        .login-link a:hover {

            text-decoration:underline;

        }





        /* MODAL */

        .modal-overlay {


            display:none;

            position:fixed;

            width:100%;

            height:100%;

            background:rgba(0,0,0,0.65);

            backdrop-filter:blur(3px);

            align-items:center;

            justify-content:center;

            z-index:10;


        }



        .modal-box {


            width:420px;

            background:white;

            border-radius:8px;

            padding:35px;

            text-align:center;

            box-shadow:0 15px 40px rgba(0,0,0,0.4);


        }



        .modal-box h4 {


            color:#3d9970;

            font-size:23px;

            line-height:1.3;

            margin-bottom:35px;


        }



        .status {


            font-size:18px;

            margin-bottom:40px;

            color:#888;


        }



        .status strong {


            color:#111;


        }



        .modal-link {


            font-size:14px;

            color:#999;


        }



        .modal-link a {


            color:#2e8b57;

            text-decoration:none;

            font-weight:600;


        }



        .modal-link a:hover {


            text-decoration:underline;


        }




        @media(max-width:450px){


            .register-container{

                width:90%;

            }


        }


    </style>


</head>


<body>



<div class="register-container">



    <img src="/images/logo-ifpr.png" class="logo">



    <h1>SIGEP</h1>

    <h2>PLENÁRIO DIGITAL</h2>


    <h3>CADASTRO</h3>




    <form id="cadastroForm">



        <div class="input-group">

            <input type="text" placeholder="Siape" required>

        </div>



        <div class="input-group">

            <input type="text" placeholder="Nome completo" required>

        </div>



        <div class="input-group">

            <input type="email" placeholder="Email institucional" required>

        </div>



        <div class="input-group">

            <input type="password" placeholder="Senha" required>

        </div>



        <div class="input-group">

            <input type="password" placeholder="Confirmar senha" required>

        </div>




        <button type="submit" class="btn btn-register">

            CADASTRAR

        </button>




    </form>



    <div class="login-link">

        Já é cadastrado?

        <a href="/">

            Realizar login

        </a>

    </div>



</div>





<!-- MODAL -->


<div class="modal-overlay" id="modalSucesso">


    <div class="modal-box">


        <h4>

            CADASTRO REALIZADO COM<br>

            SUCESSO!

        </h4>



        <div class="status">

            <strong>STATUS ATUAL:</strong> PENDENTE

        </div>



        <div class="modal-link">

            terminou o cadastro?

            <a href="/">

                Ir para página de login

            </a>

        </div>


    </div>



</div>





<script>


document
.getElementById("cadastroForm")
.addEventListener("submit", function(event){


    event.preventDefault();


    document
    .getElementById("modalSucesso")
    .style.display = "flex";


});


</script>



</body>

</html>