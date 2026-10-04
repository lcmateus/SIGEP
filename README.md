# SIGEP - Sistema de Gestão da Ética Pública

![Screenshot From 2026-04-26 00-15-44](https://github.com/user-attachments/assets/e0935a01-1817-4ab6-a817-fc8d350f579b)

# Introdução

## O que é o SIGEP?

SIGEP é a abreviação para Sistema de Gestão da Ética Pública. Se trata de um sistema web responsável por gerenciar os processos recebidos pela Comissão de Ética do Instituto Federal do Paraná (IFPR). Este sistema está sendo elaborado como projeto final do curso Técnico em Informática referente à disciplina de Projeto e Desenvolvimento de Sistemas.

## Fluxo de um processo

A jornada de uma denúncia dentro da Comissão pode ser dividida em conco grandes etapas, conforme a imagem abaixo:

![Screenshot From 2026-05-01 11-05-08](https://github.com/user-attachments/assets/025be9d2-ee02-4381-98a8-1ed03e400613)

A instituição faz uso de ferramentas poderosas como o Fala.Br, por onde as denúncias entram, e o SEI (sigla para Sistema Eletrônico de Informações), onde as denúncias são transformadas em processos administrativos. No entanto, nenhuma dessas duas ferramentas ajuda a gerenciar o trabalho interno da Comissão (como os processos são distribuídos, como as votações são feitas e registradas, etc.).

## O Problema

Atualmente, o gerenciamento das demandas éticas do IFPR é feito de forma manual: utiliza-se planilhas para controle de prazos e etapas, e o envio de documentos e comunicações ocorre por e-mail. A secretária geral precisa distribuir os processos entre os relatores manualmente e acompanhar o andamento de cada um sem uma ferramenta centralizada. Com forma de organização pode ocorrer o atrasado da tramitação dos processos, há o risco da perda de prazos, dificuldade em localizar o histórico e o status de cada demanda, a possibilidade de confusões na distribuição e na comunicação entre os membros e a má distribuição das funções de relator entre os membros.

## Propósito do Sistema

O propósito do SIGEP é automatizar e centralizar o fluxo de trabalho da Comissão de Ética. Com ele, será possível:

- Cadastro e autenticação de usuários (membros da comissão, secretária, presidente);
- Cadastro de processos com tipificação e partes envolvidas;
- Análise de admissibilidade;
- Sorteio/designação automática de relator;
- Definição automática de prazos conforme etapas regimentais;
- Área do relator: elaboração de relatório preliminar;
- Módulo de votação (aberta ou fechada);
- Registro de decisão final e encaminhamentos;
- Painel de acompanhamento (dashboard) com status dos processos;
- Notificações por e-mail sobre prazos e movimentações.

## Resultados esperados

Acreditamos que, com a implementação do SIGEP e, por consequência, a agilização do gerenciamento das demandas e o aumento da eficiência das atividades da Comissão, contribuímos para um ambiente profissional mais seguro, transparente e em conformidade com as normas éticas institucionais.

# Especificações

## Tecnologias utilizadas

- Linguagem de programação: PHP 8.5.10
- Framework: Laravel 13
- Gerenciador de dependências: Composer v2.10.13
- Para executar códigos JavaScript: Node.js v.26.9.0
- Sistema gerenciador de banco de dados: MySQL 8.4 LTS
- Sistema Operacional: Windows 11 25H2
- Gerenciamento de versão: Git 2.55.0/GitHub
- Editor de código: Visual Studio Code 1.138

## Requisitos mínimos

- 100 GB de armazenamento em disco
- 16 GB de memória RAM

# Como executar o projeto?

1. Abra o terminal e clone o repositório no diretório desejado
   ``git clone https://github.com/lcmateus/SIGEP.git``
3. Abra o explorador de arquivos e abra o projeto;
4. Renomeie o arquivo `.env.example` para `.env`;
5. Abra o arquivo `.env`, altere o atributo `"DB_PASSWORD"` para a senha do seu banco de dados e salve o arquivo;
6. Volte ao terminal e acesse o diretório do projeto;
7. Execute o comando `composer update` para atualizar as versões das dependências;
9. Execute o comando `php artisan key:generate` para gerar a chave de criptografia do banco de dados;
10. Execute o comando `php artisan migrate` para criar as tabelas no banco de dados;
11. Execute o comando `php artisan serve` para rodar o servidor de desenvolvimento local.

# Responsáveis

## Desenvolvedores

Lucas Canestraro Mateus de Oliveira

Miguel Marques Borges

Raphaelly Daphny Ferreira

## Orientadores

Orientador: Fábio Albini

Co-orientador: Fernando Roberto Amorim Souza

# Saiba mais sobre o projeto

Vídeo de apresentação do projeto: https://www.youtube.com/watch?v=Ss2oeBPBhDI

Primeiro vídeo de demonstração das telas: https://youtu.be/63aGhfENQqE?si=Ho7h472oMOOB86Dc

Vídeo de apresentação da versão Alpha: https://youtu.be/9ALqNUudgDY?si=Louvc0eiGgz-AGJ-

Primeiro vídeo de demonstração das telas: https://youtu.be/63aGhfENQqE?si=Ho7h472oMOOB86Dc

Vídeo de apresentação da versão Alpha: https://youtu.be/9ALqNUudgDY?si=Louvc0eiGgz-AGJ-
