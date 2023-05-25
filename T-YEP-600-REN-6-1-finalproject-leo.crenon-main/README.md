# FileFlow                                                                                                                                                                                                                                            
## API
#### Requirements
- PHP 8.1 and all the modules required by symfony
- Composer: see [https://getcomposer.org/]
- MariaDB
- Symfony CLI [https://symfony.com/download]

### Installation
```sh
git clone git@github.com:EpitechMscProPromo2024/T-YEP-600-REN-6-1-finalproject-leo.crenon.git
cd T-YEP-600-REN-6-1-finalproject-leo.crenon/BACK/
composer install
symfony check:requirements
```
If the last command doesn't show any requierment error then your almost ready to develop ! :)

### Configuration
You will have to generate key pair to be able to use jwt token.
To do so execute the following commands:
```sh
php bin/console lexik:jwt:generate-keypair
```
The console shloud responds with "[Ok] Done !"

You will have to edit the .env file and modify the DATABASE_URL according to your credentials.
```sh
DATABASE_URL="mysql://YOUR_USERNAME:YOUR_PASSWORD@127.0.0.1:3306/yearendbdd"
```

Very last thing. You have to create the database and its structure.
```sh
php bin/console d:d:c
php bin/console d:s:u --force
```

Enjoy !

## Flutter
Install Flutter version 3+ https://docs.flutter.dev/get-started/install

```sh
flutter doctor
```

```sh
cd front/
```

```sh
flutter run
```

