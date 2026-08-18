<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>SIGEP - Recuperação de senha</title>

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

background:rgba(0,128,96,0.85);

backdrop-filter:blur(3px);

}



.container{

position:relative;

width:380px;

background:#f2f2f2;

border-radius:15px;

padding:30px;

text-align:center;

box-shadow:0 15px 40px rgba(0,0,0,0.3);

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

margin-bottom:25px;

}



h3{

font-size:20px;

margin-bottom:20px;

}



.texto{

font-size:13px;

color:#777;

margin-bottom:20px;

line-height:1.5;

}



.input-group{

margin-bottom:15px;

}



input{

width:100%;

padding:12px;

border-radius:8px;

border:1px solid #ccc;

outline:none;

font-size:14px;

}



input:focus{

border-color:#2e8b57;

box-shadow:0 0 5px rgba(46,139,87,0.4);

}



.btn{

width:100%;

padding:12px;

border:none;

border-radius:8px;

cursor:pointer;

font-weight:600;

margin-top:10px;

transition:.3s;

}



.btn-enviar{

background:#2e8b57;

color:white;

}



.btn-enviar:hover{

background:#256f47;

}



.voltar{

margin-top:15px;

font-size:13px;

}



.voltar a{

color:#2e8b57;

text-decoration:none;

font-weight:600;

}



.voltar a:hover{

text-decoration:underline;

}



/* MODAL */


.modal{

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



.modal-box{

background:white;

width:400px;

padding:35px;

border-radius:10px;

text-align:center;

}



.modal-box h4{

color:#3d9970;

font-size:22px;

margin-bottom:20px;

}



.modal-box p{

color:#777;

font-size:14px;

line-height:1.5;

}



.modal-box a{

display:block;

margin-top:25px;

color:#2e8b57;

font-weight:600;

text-decoration:none;

}



</style>


</head>



<body>


<div class="container">


<img src="/images/logo-ifpr.png" class="logo">


<h1>SIGEP</h1>

<h2>PLENÁRIO DIGITAL</h2>


<h3>RECUPERAÇÃO DE SENHA</h3>



<p class="texto">

Informe seus dados institucionais para recuperar o acesso.

</p>



<form id="recuperarForm">


<div class="input-group">

<input type="text" placeholder="SIAPE">

</div>



<div class="input-group">

<input type="email" placeholder="E-mail institucional">

</div>



<button class="btn btn-enviar">

ENVIAR SOLICITAÇÃO

</button>


</form>



<div class="voltar">

<a href="/">

Voltar para login

</a>

</div>



</div>





<div class="modal" id="modal">


<div class="modal-box">


<h4>

SOLICITAÇÃO ENVIADA!

</h4>


<p>

Caso os dados estejam corretos,
um link de recuperação será enviado
para seu e-mail institucional.

</p>



<a href="/">

Voltar para login

</a>


</div>


</div>





<script>


document
.getElementById("recuperarForm")
.addEventListener("submit",function(e){


e.preventDefault();


document
.getElementById("modal")
.style.display="flex";


});


</script>



</body>

</html>