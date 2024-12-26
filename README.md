# Gestão de Usuários e Despesas

## DESCRIÇÃO
- Este sistema foi desenvolvido para gerenciar players e Guildas de forma eficiente e organizada. 
- Permite registrar, visualizar, atualizar e excluir informações de players, além de controlar os Guildas e quais players terao acesso.



## SITE PARA ACESSO
- http://15.228.82.7/



## OBS
- Existe *ROLE* para mestre e player.
- Player: So pode visualizar qual Guilda esta e Editar o seu usuario
- Mestre: Pode visualizar, editar, excluir e balancear Guildas e pode visualizar, editar, excluir Players.


## Usuarios
- MESTRE
    ```
        LOGIN: admin@admin.com
        SENHA: 12345678
    ```
- PLAYER
    ```
        LOGIN: player@player.com
        SENHA: 12345678
    ```

## INSTALAÇÃO

### Pré-requisitos
Certifique-se de ter as seguintes ferramentas instaladas em seu sistema:
- PHP ^8.2
- Composer

### Passo a Passo

1. **Clone o Repositório**
- git clone [https://github.com/seu-usuario/teste_yetz.git](https://github.com/FlavianoMatozinhos/teste_yetz.git)
- cd teste_yetz


2. **Configuração do Ambiente**
- Copie o arquivo `.env.example` para `.env`:
  ```
  cp .env.example .env
  ```

- Configure seu arquivo `.env` com as informações necessárias, incluindo configurações de banco de dados
- 


3. **Instalação das Dependências**
    ```
        - composer install (No arquivo BackEnd - api)
    ```

4. **Geração da Chave de Aplicação**
    ```
        - php artisan key:generate - (No arquivo BackEnd - api)
    ```

5. **Migração do Banco de Dados**
    ```
        - php artisan migrate - (No arquivo BackEnd - api)
    ```

6. **Instalação do Passport**
    ```
        - composer require laravel/passport - (No arquivo BackEnd - api)
    ```

7. **Instalação do Passport pt2**
    ```
    - php artisan passport:install
    ```
OBS(Irá aparecer a opção de criar migrations, digitar YES /  Irá aparecer a opção de Criar KEYS para o Client, digitar YES) - (No arquivo BackEnd - api)


8. **Instalação do Sanctum**
    ```
        - composer require laravel/sanctum - (No arquivo BackEnd - api)
    ```


9. **Instalação do Swagger**
    ```
        - composer require darkaonline/l5-swagger - (No arquivo BackEnd - api)
    ```
    ```
         - php artisan l5-swagger:generate - (No arquivo BackEnd - api)
    ```


## TESTES UNITARIOS DO BACKEND (arquivo api)
- Para que os testes ocorram de forma correta

1. Precisa ter os arquivos .env.testing.
    ```
        APP_ENV=testing
        APP_KEY=base64:
        DB_CONNECTION=sqlite
        DB_DATABASE=:memory:
        CACHE_DRIVER=array
        SESSION_DRIVER=array
    ```

2. É preciso criar uma chave para o env de teste.
    ```
        php artisan key:generate --env=teste
    ```

3. Ajustar o arquivo phpunit.xml para que os testes ocorram em um banco de dados "ficticio".
    ```
        <php>
            <env name="APP_ENV" value="teste"/>
            <env name="BCRYPT_ROUNDS" value="4"/>
            <env name="CACHE_DRIVER" value="array"/>
            <env name="DB_CONNECTION" value="sqlite"/>
            <env name="DB_DATABASE" value=":memory:"/>
            <env name="MAIL_MAILER" value="array"/>
            <env name="QUEUE_CONNECTION" value="sync"/>
            <env name="SESSION_DRIVER" value="array"/>
            <env name="TELESCOPE_ENABLED" value="false"/>
        </php>
    ```
4. Criar as tabelas do banco de dados.
    ```
        php artisan migrate --env=testing  
    ```

5. Agora é só rodar os teste
    ```
        php artisan test 
    ```

## Gerar dados automaticos no banco (arquivo api)
    ```
        php artisan db:seed --class=RoleSeeder
    ```

    ```
        php artisan db:seed --class=ClassSeeder
    ```


## USO
- Acessando a tela principal, você será redirecionado para a tela de Login, basta informar email e senha.
![login](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/login.PNG?raw=true)



- Caso não tenha usuario cadastro, ir através da barra de navegação e clicar em Register, e assim que criar o usuario, será redirecionado para a tela de login.
![register](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/cadastro.PNG?raw=true)



- Após feito login, será redirecionado para a pagina de Home, que só é acessivel através do Login.


## MESTRE

-Tela de Home
![home_mestre](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Home.PNG?raw=true)



- Clicando em Criar Guilda na tela de HOME, você terá acesso para cadastrar Guildas ao seu sistema.
![mestre_criar_guilda_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Criar_Guilda.PNG?raw=true)



- Tela de Cadastro da Guilda, sendo obrigatorios o nome, minimo e maximo de players.
![mestre_criar_guilda](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Criar_Guilda%202.PNG?raw=true)



- Clicando em Balancear Guildas na tela de HOME, o sistema ira balancear os Players que estao confirmados para participar atraves do seu XP.
![mestre_balancear](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Balancear_Guilda.PNG?raw=true)



- Clicando em Excluir um player na tela de HOME, aparecerá um alerta perguntando se deseja mesmo fazer essa exclusão.
![mestre_excluir_player_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Excluir_Player_1.PNG?raw=true)
![mestre_excluir_player_alert](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Excluir_Player_2.PNG?raw=true)



- Clicando em Visualizar um player na tela de HOME, sera redirecionado para a tela do Player e com suas respectivas informacoes.
![mestre_player_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Excluir_Player_1.PNG?raw=true)



- Tela de Visualizacao do Player
![mestre_player](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_player_2.PNG?raw=true)



- Dentro da Tela de Visualizacao do Player e possivel Editar as informacoes do jogador
![mestre_player_edit_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_player_2.PNG?raw=true)



- Tela de Editar o Player
![mestre_player_edit](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Editar_player.PNG?raw=true)



- Clicando em Excluir um Guilda na tela de HOME, aparecerá um alerta perguntando se deseja mesmo fazer essa exclusão.
![mestre_excluir_guilda_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Guilda.PNG?raw=true)
![mestre_excluir_guilda_alert](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Excluir_Guilda_1.PNG?raw=true)



- Clicando em Visualizar uma Guilda na tela de HOME, sera redirecionado para a tela da Guilda e com suas respectivas informacoes.
![mestre_guilda_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Guilda.PNG?raw=true)



- Tela de Visualizacao da Guilda
![mestre_guilda](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_Guilda_1.PNG?raw=true)



- Clicando em Editar uma Guilda na tela de HOME, sera redirecionado para a tela de Editar Guilda.
![mestre_guilda_edit_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Guilda.PNG?raw=true)



- Tela de Editar Guilda
![mestre_guilda_edit](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Editar_Guilda.PNG?raw=true)


## Player

- Tela de Home
![home_player](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Player.PNG?raw=true)



- Clicando em Visualizar na tabela de Guildas
![player_guilda_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_Guilda_2_Player.PNG?raw=true)



- Tela de Visulizar Guilda, e possivel visualizar todos os jogadores que estao nesta guilda e suas respectivas classes
![player_guilda](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_Guilda_1.PNG?raw=true)



-  Clicando em Visualizar na tabela de Players
![player_player_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_player_1_player.PNG?raw=true)



- Tela de Visulizar Player, e possivel Visualizar o seu perfil
![player_player](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_player_1_player.PNG?raw=true)



- Tela de Visulizar Player, e possivel Editar o seu perfil, clicando em *Editar Jogador*
![player_player_edit_view](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Visualizar_player_2.PNG?raw=true)



- Tela de Editar Player
![player_player_edit](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Editar_player.PNG?raw=true)



- Dentro da *Tela de Home*, na tabela de *Players*, e possivel confirmar se voce quer entrar em uma Guilda ou Nao.
![player_player_confirm](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Nao_Confirmado_player.PNG?raw=true)



- Dentro da *Tela de Home*, na tabela de *Players*, caso ja esteja confirmado, tambem e possivel retirar essa confirmacao.
  ![player_player_retirar_confirm](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Confirmar_player.PNG?raw=true)



- Para fazer Logout, basta clicar na barra de navegação "Logout"
![logout](https://github.com/FlavianoMatozinhos/teste_yetz/blob/main/public/img/Logout.PNG?raw=true)



## CURLS da API
- post /api/register
    ```
        curl --location 'http://localhost:8000/api/register' \
        --header 'Accept: application/json' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "name": "teste",
            "email": "teste5@example.com",
            "password": "senha123",
            "password_confirmation": "senha123",
            "class_id": 1,
            "guild_id": "",
            "xp": "",
            "confirmed": false
        }'
    ```


- post /api/login
    ```
        curl --location 'http://localhost:8000/api/login' \
        --header 'Content-Type: application/json' \
        --header 'Accept: application/json' \
        --data-raw '{
            "email": "teste@teste.com",
            "password": "12345678"
          }'
    ```


- get /api/players
    ```
        curl --location 'http://localhost:8000/api/players' \
        --header 'Accept: application/json' \
        --header 'Content-Type: application/json' \
        --header 'Authorization: Bearer ••••••'
    ```


- put /api/players/1
    ```
        curl --location --request PUT 'http://localhost:8000/api/players/1' \
        --header 'Accept: application/json' \
        --header 'Content-Type: application/json' \
        --header 'Authorization: Bearer ' \
        --data-raw '{
            "name": "Novo Nome",
            "email": "novoemail@example.com",
            "password": "novasenha123",
            "password_confirmation": "novasenha123",
            "class_id": 3,
            "guild_id": "",
            "xp": "10",
            "confirmed": true,
            "role_id": 2
        }'
    ```


- delete /api/players/1
    ```
        curl --location --request DELETE 'http://localhost:8000/api/players/1' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer '
    ```


- get /api/classes
    ```
        curl --location 'http://localhost:8000/api/classes' \
        --header 'Accept: application/json' \
        --header 'Content-Type: application/json' \
        --header 'Authorization: Bearer '
    ```


- post /api/classes
    ```
        curl --location 'http://localhost:8000/api/classes' \
        --header 'Content-Type: application/json' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer ' \
        --data '{
            "name": "teste5"
        }'
    ```


- put /api/classes/1
    ```
        curl --location --request PUT 'http://localhost:8000/api/classes/1' \
        --header 'Content-Type: application/json' \
        --header 'Authorization: Bearer ' \
        --data '{
            "name": "teste6"
        }'
    ```


- delete /api/classes/1
    ```
        curl --location --request DELETE 'http://localhost:8000/api/classes/1' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer '
    ```


- get /api/guilds
    ```
        curl --location 'http://localhost:8000/api/guilds' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer '
    ```


- post /api/guilds
    ```
        curl --location 'http://localhost:8000/api/guilds' \
        --header 'Accept: application/json' \
        --header 'Content-Type: application/json' \
        --header 'Authorization: Bearer ' \
        --data '{
            "name": "Guilda dos Guerraaa",
            "min_players": 2,
            "max_players": 5
        }'
    ```


- put /api/guilds/1
    ```
        curl --location --request PUT 'http://localhost:8000/api/guilds/1' \
        --header 'Content-Type: application/json' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer ' \
        --data '{
            "name": "Guilda dos Campeões",
            "max_players": 6,
            "min_players": 1
        }'
    ```


- delete /api/guilds/1
    ```
        curl --location --request DELETE 'http://localhost:8000/api/guilds/1' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer ' \
        --header 'Cookie: XSRF-TOKEN='
    ```
