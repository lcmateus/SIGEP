<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SIGEP - Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Inter',sans-serif;
        }

        body{
            height:100vh;
            background:url('/images/bg-login.jpg') no-repeat center center/cover;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        body::before{
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            background:rgba(0,128,96,.85);
            backdrop-filter:blur(3px);
        }

        .register-container{
            position:relative;
            width:430px;
            background:#f2f2f2;
            border-radius:15px;
            padding:30px;
            text-align:center;
            box-shadow:0 15px 40px rgba(0,0,0,.3);
            z-index:1;
        }

        .logo{
            width:120px;
            margin-bottom:10px;
        }

        h1{
            font-size:22px;
            font-weight:600;
            margin-bottom:5px;
        }

        h2{
            font-size:16px;
            color:#555;
            margin-bottom:8px;
        }

        h3{
            font-size:24px;
            margin-bottom:25px;
            font-weight:700;
            color:#111;
        }

        .input-group{
            margin-bottom:15px;
        }

        input{
            width:100%;
            padding:12px;
            border:1px solid #ccc;
            border-radius:8px;
            font-size:14px;
            outline:none;
            transition:.3s;
        }

        input:focus{
            border-color:#2e8b57;
            box-shadow:0 0 5px rgba(46,139,87,.35);
        }

        .btn{
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-size:15px;
            font-weight:600;
            transition:.3s;
            margin-top:8px;
        }

        .btn-register{
            background:#2e8b57;
            color:#fff;
        }

        .btn-register:hover{
            background:#256f47;
        }

        .login-link{
            margin-top:18px;
            font-size:14px;
            color:#666;
        }

        .login-link a{
            color:#2e8b57;
            text-decoration:none;
            font-weight:600;
        }

        .login-link a:hover{
            text-decoration:underline;
        }

        @media (max-width:450px){

            .register-container{
                width:92%;
                padding:25px;
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

    <form>

        <div class="input-group">
            <input type="text" placeholder="SIAPE">
        </div>

        <div class="input-group">
            <input type="text" placeholder="Nome completo">
        </div>

        <div class="input-group">
            <input type="email" placeholder="E-mail institucional">
        </div>

        <div class="input-group">
            <input type="password" placeholder="Senha">
        </div>

        <div class="input-group">
            <input type="password" placeholder="Confirmar senha">
        </div>

        <button type="submit" class="btn btn-register">
            CADASTRAR
        </button>

        <div class="login-link">
            Já possui uma conta?
            <a href="/">Realizar login</a>
        </div>

    </form>

</div>

</body>
</html>