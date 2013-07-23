#Walrus
> _Brace Yourselves Walrus is Coming !_
=====================

![Walrus](https://github.com/E-Wok/Walrus/blob/master/Walrus.png?raw=true "Walrus is comming !")

### Team :
Jusqu'a 6 personnes, il faut des gens motivés et bosseur.

Rendre le repo ouvert par la suite ? Faire gaffe à ce que personne vienne nous piquer des idées et du code dans la promo.

### A utilisé
PHP 5.3.X

Build [Composer] (http://getcomposer.org/)

Norme [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/fr/PSR-0.md)

Test unitaire [PHPUnit] (https://github.com/sebastianbergmann/phpunit/)

Documentation [Doxygen] (http://www.stack.nl/~dimitri/doxygen/)

Render: [Smarty] (http://www.smarty.net/)
        [Twig] (http://twig.sensiolabs.org/)
        [HAML] (https://code.google.com/p/phamlp/)
        [Mustache] (http://mustache.github.io/)
        
Preprocessing CSS: [SASS] (http://sass-lang.com/)

Preprocessing Javascript: [CofeeScript] (http://coffeescript.org/)

Framework javascript : [jQuery] (http://jquery.com/)

### Features :
gestion des routes

Creation de Web service / API ?

Outil de long polling ?

Gestion d'ACL (droits utilisateur) ?

Generateur de formulaire ?

Filer ?

Query builder ?

Library easy load ?

Light load ?

###Arborescence
=====================
```
 Walrus/
  ├── app/
  │   ├── config/
  │   ├── components/
  │   └── log/
  │   
  ├── src/
  │   ├── config/
  │   ├── controllers/
  │   ├── views/
  │   └── entities/
  ├── vendors/
  └── web/
      ├── walrus/
      └── index.php
```

#####app
Coeur de Walrus

#####src
Modele/Vue/Controleur du projet

#####vendors
Composant externe a Walrus

#####web
Fichiers accessibles coté client

###Ordre d'execution
=====================
1. Gestion des erreurs
2. Variables d'environement
3. Configuration
4. Lib / autres fonctions generale
4. Base de donnée
5. Routeur
6. Controleur
7. Modele
8. Vue
