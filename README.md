#Walrus
> _Brace Yourselves Walrus is Coming !_
=====================

![Walrus](https://github.com/E-Wok/Walrus/blob/master/Walrus.png?raw=true "Walrus is comming !")

### Team
Jusqu'a 6 personnes, il faut des gens motivés et bosseur.

### Miscelaneous
Le repo sera ouvers apres la soutenance.

### Composantes
#####A utilisé (obligatoire):
PHP 5.3.X

Build [Composer] (http://getcomposer.org/) ([Tutoriel] (http://net.tutsplus.com/tutorials/php/easy-package-management-with-composer/?search_index=28))

Norme [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/fr/PSR-0.md)
([Tutoriel 1] (https://github.com/php-fig/fig-standards/blob/master/accepted/fr/PSR-0.md),
[Tutoriel 2] (http://net.tutsplus.com/tutorials/php/psr-huh/?search_index=32))

Test unitaire [PHPUnit] (https://github.com/sebastianbergmann/phpunit/)
([Tutoriel] (http://net.tutsplus.com/tutorials/php/how-to-write-testable-and-maintainable-code-in-php/?search_index=3),
[Video] (http://net.tutsplus.com/tutorials/php/hands-on-unit-testing-with-phpunit/?search_index=1))

Documentation [Doxygen] (http://www.stack.nl/~dimitri/doxygen/)

Render: [Smarty] (http://www.smarty.net/)
        [Twig] (http://twig.sensiolabs.org/)
        [HAML] (https://code.google.com/p/phamlp/)
        [Mustache] (http://mustache.github.io/)

#####Kikoulol Front-End (useless):
Preprocessing CSS: [SASS] (http://sass-lang.com/)

Preprocessing Javascript: [CofeeScript] (http://coffeescript.org/)

Framework javascript : [jQuery] (http://jquery.com/)

### Features :
gestion des routes ?

Creation de Web service / API ?

Outil de long polling ?

Gestion d'ACL (droits utilisateur) ?

Generateur de formulaire ?

Filer ?

Query builder ?

Caching ?

###Arborescence (Symfony like)
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
