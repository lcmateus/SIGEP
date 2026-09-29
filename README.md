<h1>SIGEP - Sistema de Gestão da Ética Pública</h1>

<img width="212" height="96" alt="Screenshot From 2026-04-26 00-15-44" src="https://github.com/user-attachments/assets/e0935a01-1817-4ab6-a817-fc8d350f579b" />

<h1>Introdução</h1>

<h2>O que é o SIGEP?</h2>

SIGEP é a abreviação para Sistema de Gestão da Ética Pública. Se trata de um sistema web responsável por gerenciar os processos recebidos pela Comissão de Ética do Instituto Federal do Paraná (IFPR). Este sistema está sendo elaborado como projeto final do curso Técnico em Informática referente à disciplina de Projeto e Desenvolvimento de Sistemas.

<h2>Fluxo de um processo</h2>

A jornada de uma denúncia dentro da Comissão pode ser dividida em conco grandes etapas, conforme a imagem abaixo:

<img width="1408" height="803" alt="Screenshot From 2026-05-01 11-05-08" src="https://github.com/user-attachments/assets/025be9d2-ee02-4381-98a8-1ed03e400613" />

A instituição faz uso de ferramentas poderosas como o Fala.Br, por onde as denúncias entram, e o SEI (sigla para Sistema Eletrônico de Informações), onde as denúncias são transformadas em processos administrativos. No entanto, nenhuma dessas duas ferramentas ajuda a gerenciar o trabalho interno da Comissão (como os processos são distribuídos, como as votações são feitas e registradas, etc.).

<h2>O Problema</h2>

Atualmente, o gerenciamento das demandas éticas do IFPR é feito de forma manual: utiliza-se planilhas para controle de prazos e etapas, e o envio de documentos e comunicações ocorre por e-mail. A secretária geral precisa distribuir os processos entre os relatores manualmente e acompanhar o andamento de cada um sem uma ferramenta centralizada. Com forma de organização pode ocorrer o atrasado da tramitação dos processos, há o risco da perda de prazos, dificuldade em localizar o histórico e o status de cada demanda, a possibilidade de confusões na distribuição e na comunicação entre os membros e a má distribuição das funções de relator entre os membros.

<h1>A ferramenta</h1>

<h2>Propósito do Sistema</h2>

O propósito do SIGEP é automatizar e centralizar o fluxo de trabalho da Comissão de Ética. Com ele, será possível:

<ul>
    <li>Cadastro e autenticação de usuários (membros da comissão, secretária, presidente);</li>
    <li>Cadastro de processos com tipificação e partes envolvidas;</li>
    <li>Análise de admissibilidade;</li>
    <li>Sorteio/designação automática de relator;</li>
    <li>Definição automática de prazos conforme etapas regimentais;</li>
    <li>Área do relator: elaboração de relatório preliminar;</li>
    <li>Módulo de votação (aberta ou fechada);</li>
    <li>Registro de decisão final e encaminhamentos;</li>
    <li>Painel de acompanhamento (dashboard) com status dos processos;</li>
    <li>Notificações por e-mail sobre prazos e movimentações.</li>
</ul>

<h2>Perfis de usuários</h2>

Os perfis de usuários do Sistema é um espelho da Comissão na vida real, cada um com perfis bem definidos.

<table>
    <tr>
        <th>Perfis de membros</th>
        <th>Responsabilidade chave</th>
    </tr>
    <tr>
        <td>Administrador (Secretário(a) Executivo(a))</td>
        <td>Insere os processos e gerencia os usuários</td>
    </tr>
    <tr>
        <td>Membro titular</td>
        <td>Vota em cada etapa e atua como relator quando selecionado</td>
    </tr>
    <tr>
        <td>Relator</td>
        <td>Responsável pelo andamento do processo (anexar arquivos, criar votações, etc.)</td>
    </tr>
    <tr>
        <td>Presidente da CE</td>
        <td>Exerce voto de minerva (desempate) e atua como relator quando selecionado</td>
    </tr>
</table>

<h1>Conclusão</h1>

<h2>Resultados esperados</h2>

Acreditamos que, com a implementação do SIGEP e, por consequência, a agilização do gerenciamento das demandas e o aumento da eficiência das atividades da Comissão, contribuímos para um ambiente profissional mais seguro, transparente e em conformidade com as normas éticas institucionais.

<h1>Especificações</h1>

<h2>Tecnologias utilizadas</h2>

<ul>
    <li>PHP 8.5.10</li>
    <li>Laravel 13</li>
    <li>Composer v2.10.13</li>
    <li>Node.js v.26.9.0</li>
    <li>MySQL 8.4 LTS</li>
    <li>Sistema Operacional: Windows 11 25H2</li>
    <li>Gerenciamento de versão: Git 2.55.0/GitHub</li>
    <li>Visual Studio Code 1.138</li>
</ul>

<h2>Requisitos mínimos</h2>

<ul>
    <li>100 GB de armazenamento em disco</li>
    <li>16 GB de memória RAM</li>
</ul>

<h1>Como executar o projeto?</h1>
<ol>
    <li>Abra o terminal e clone o repositório;</li>
    <li>Abra o explorador de arquivos e abra o projeto;</li>
    <li>renomeie o arquivo .env.example para .env;</li>
    <li>abra o arquivo .env, altere o atributo "DB_PASSWORD" para a senha do seu banco de dados e salve o arquivo;</li>
    <li>volte ao terminal e acesse o diretório do projeto;</li>
    <li>execute o comando composer update e depois composer upgrade</li>
    <li>execute o comando php artisan key:generate;</li>
    <li>php artisan migrate;</li>
    <li>php artisan serve.</li>
</ol>
<p></p>

<h1>Saiba mais sobre o projeto</h1>
    
<p>Vídeo de apresentação do projeto: https://www.youtube.com/watch?v=Ss2oeBPBhDI</p> 
<p>Primeiro vídeo de demonstração das telas: https://youtu.be/63aGhfENQqE?si=Ho7h472oMOOB86Dc</p>
<p>Vídeo de apresentação da versão Alpha: https://youtu.be/9ALqNUudgDY?si=Louvc0eiGgz-AGJ-</p>
