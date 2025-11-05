Instalação

1. Baixe o XAMPP versão 8.2
2. Instale o Node.js para ativar o Tailwind CSS
3. Baixe o composer (https://getcomposer.org/Composer-Setup.exe)
4. Clone o repositório
5. No diretório do repositório digite npm install e depois composer install

Setup do ambiente

1. Abra um terminal e faça as migrations: php artisan migrate (esse comando cria as tabelas no banco de dados)
2. Alimente as tabelas com dados iniciais (seeders):
   php artisan db:seed --class=RolesSeeder
   php artisan db:seed --class=UsersSeeder

Executando o projeto

1. Abra dois terminais
2. Em um deles digite npm run dev para compilar o CSS do Tailwind e deixe rodando
3. No outro terminal digite php artisan serve
4. Acesse o navegador em localhost:8000
