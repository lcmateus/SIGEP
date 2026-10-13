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

O propósito do SIGEP é automatizar e centralizar o fluxo de trabalho da Comissão de Ética. Com ele, o usuário pode:

- Efetuar o auto-cadastro;
- Validar usuários, no caso do administrador;
- Anexar e baixar documentos (PDF exportado do SEI, relatórios de etapas, diligências, etc.);
- Acompanhar o andamento dos processos sob relatoria;
- Consultar processos públicos;
- Filtrar e exportar os resultados dos processos;
- Encaminhar/arquivar processos;
- Consultar resultados.

  
Além disso, o próprio sistema realiza tarefas de maneira automática, como:

- Designação de relatores, tendo como critério o menor número de processos sob relatoria;
- Definição de prazos para votações;
- Registro de resultados de votações;
- Envio de notificações por e-mail sobre prazos e movimentações.

## Resultados esperados

Acreditamos que, com a implementação do SIGEP e, por consequência, a agilização do gerenciamento das demandas e o aumento da eficiência das atividades da Comissão, contribuímos para um ambiente profissional mais seguro, transparente e em conformidade com as normas éticas institucionais.

# Especificações

## Tecnologias utilizadas

- Linguagem de programação: PHP 8.5.11
- Framework: Laravel 13
- Gerenciador de dependências: Composer v2.10.13
- Para executar códigos JavaScript: Node.js v.24.21.0
- Sistema gerenciador de banco de dados: MySQL 8.4.11 LTS
- Sistema Operacional: Windows 11 25H2
- Gerenciamento de versão: Git 2.56.0.2
- Editor de código: Visual Studio Code 1.141.1

## Requisitos mínimos

- 100 GB de armazenamento em disco
- 16 GB de memória RAM

# Como instalar e executar o projeto?

## 1. Instaladores

Antes de começar, verifique se a pasta `/instaladores` contém os seguintes arquivos:

| Arquivo                             | Finalidade                                    |
| ----------------------------------- | --------------------------------------------- |
| `php-8.5.11-Win32-vs17-x64.zip`     | Interpretador PHP                             |
| `Composer-Setup.exe`                | Gerenciador de dependências do PHP            |
| `node-v24.21.0-x64.msi`             | Node.js e npm                                 |
| `mysql-workbench-8.4.11-winx64.msi` | Interface gráfica para gerenciamento do MySQL |
| `VSCodeUserSetup-x64-1.141.1.exe`   | Editor de código Visual Studio Code           |
| `Git-2.56.0.2-64-bit.exe`           | Controle de versão e clonagem do repositório  |

Os arquivos `.vsix` disponíveis na pasta são extensões opcionais para o Visual Studio Code.

## 2. Instalação das ferramentas

### 2.1. Instalar o PHP

1. Acesse a pasta `/instaladores`.

2. Extraia o arquivo `php-8.5.11-Win32-vs17-x64.zip` para uma pasta de sua preferência, por exemplo:

   `C:\php`

3. Localize o arquivo `php.ini-development` e faça uma cópia com o nome `php.ini`.

4. Abra o arquivo `php.ini` em um editor de texto.

5. Localize e habilite as extensões necessárias ao Laravel e ao MySQL, removendo o ponto e vírgula (`;`) do início das linhas correspondentes. Verifique, especialmente, as extensões `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql` e `zip`.

6. Adicione o diretório `C:\php` à variável de ambiente `Path` do Windows.

7. Abra um novo terminal e execute:

   ```bash
   php -v
   ```

Se a versão do PHP for exibida, a instalação foi reconhecida pelo sistema.

### 2.2. Instalar o Composer

1. Execute `Composer-Setup.exe`.
2. Durante a instalação, informe o caminho do executável `php.exe`, caso o instalador solicite. Exemplo: `C:\php\php.exe`.
3. Conclua a instalação.
4. Abra um novo terminal e execute:
   ```bash
   composer -V
   ```

### 2.3. Instalar o Node.js

1. Execute `node-v24.21.0-x64.msi`.
2. Siga as instruções do instalador, mantendo a instalação do npm habilitada.
3. Ao concluir, abra um novo terminal e execute:
   ```bash
   node -v
   npm -v
   ```

### 2.4. Instalar o MySQL Workbench

1. Execute `mysql-workbench-26.7.0-winx64.msi` e conclua a instalação, escolhendo essencialmente o Complete para instalar o MySQL Server junto.
2. Abra o MySQL Workbench e conecte-se à instância local do MySQL Server.
3. Crie um banco de dados para o projeto, por exemplo:
   ```sql
   CREATE DATABASE sigep;
   ```

O nome do banco deve corresponder ao valor configurado posteriormente no arquivo `.env`.

### 2.5. Instalar o Git

1. Execute `Git-2.56.0.2-64-bit.exe`.
2. Siga as instruções do instalador. As opções padrão são suficientes para a utilização básica do Git.
3. Abra um novo terminal e execute:
   ```bash
   git --version
   ```

### 2.6. Instalar o Visual Studio Code

1. Execute `VSCodeUserSetup-x64-1.141.1.exe`.
2. Conclua a instalação.
3. Abra o Visual Studio Code e, pelo menu **Arquivo > Abrir Pasta**, selecione o diretório do projeto após cloná-lo.

**Extensões opcionais:** os arquivos `.vsix` podem ser instalados pelo menu de extensões do VS Code, utilizando a opção **Instalar do VSIX...**. Instale apenas se necessário.

## 3. Clonar o repositório

Abra o terminal na pasta em que deseja armazenar o projeto e execute:

```bash
git clone https://github.com/lcmateus/SIGEP.git
```

Entre no diretório criado:

```bash
cd SIGEP
```

## 4. Configurar as variáveis de ambiente

1. Na raiz do projeto, localize o arquivo `.env.example`.

2. Faça uma cópia desse arquivo e nomeie-a `.env`.

3. Abra o arquivo `.env` e configure as variáveis de conexão com o banco de dados:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sigep
   DB_USERNAME=root
   DB_PASSWORD=sua_senha
   ```

   Substitua `sua_senha` pela senha definida durante a configuração do MySQL Server. Se o banco utilizar outro usuário, informe os dados correspondentes.

4. Salve o arquivo.

**Atenção:** o arquivo `.env` contém configurações privadas e não deve ser enviado ao repositório Git.

## 5. Instalar as dependências do projeto

Na raiz do projeto, execute:

```bash
composer install
```

Esse comando instala as dependências PHP definidas no `composer.lock`, quando esse arquivo estiver disponível.

## 6. Gerar a chave da aplicação

Execute:

```bash
php artisan key:generate
```

Esse comando gera a chave de criptografia da aplicação Laravel e atualiza a variável `APP_KEY` no arquivo `.env`.

## 7. Criar as tabelas do banco de dados

Certifique-se de que o MySQL Server esteja em execução e que as credenciais do `.env` estejam corretas.

Execute:

```bash
php artisan migrate
```

O Laravel executará as migrações disponíveis e criará as tabelas necessárias no banco de dados configurado.

## 8. Executar o projeto

Na raiz do projeto, execute:

```bash
php artisan serve
```

Por padrão, o Laravel disponibilizará a aplicação no endereço:

**[http://127.0.0.1:8000](http://127.0.0.1:8000)**

Abra esse endereço no navegador para acessar o SIGEP.

Para encerrar o servidor, volte ao terminal e pressione `Ctrl + C`.


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

Vídeo de apresentação da versão Beta: https://youtu.be/xgdFLs9LLyg?si=9JGC5b8kWrKS8lL8
