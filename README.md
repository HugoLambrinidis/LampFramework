# LampFramework


Generateur de controller =>

php /src/ControllerGen/ControllerGenerator.php namespace name viewName getterSetter variables * x

Orm =>
    définir paramètre de connexion bdd :

    php /src/myORM/index.php config db host user passwd driver dbname

    définir logs repo :

    php /src/myORM/index.php config logs accessLogRepo errorLogRepo

    définir path repo Model :

    php /src/myORM/index.php config model_path /path/to/model

    générer une table + modèle :

    php /src/myORM/index.php generate tableName { col type } * x