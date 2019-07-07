# Do not use Walrus in production !

Walrus framework was made as a school project and isn't maintained anymore. If you are looking for a great PHP framework try [Laravel](https://laravel.com).

<hr>

# Welcome to Walrus
> Simple, Lightweight and Fast. 
> Certainly not that big whatever factory.

<p align="center">
  <img src="https://github.com/E-Wok/Walrus/blob/master/Walrus.png?raw=true" alt="Walrus is comming !"/>
</p>

> “We need a simple PHP framework that can build web applications easily and simply without all of those features that are not necessary”

#### [Walrus Framework full documentation](https://walrus.herokuapp.com)

Walrus is a framework for building web-application in a very simple way according to the Model-View-Controller (MVC) pattern.

When it comes to building web applications we have plenty of choices. There must be around 30 or 40 PHP frameworks that are recognized as capable of building web applications in a proper way depending on the PHP community.

In those nearly 40 frameworks only a bunch of them are truly used by web developers. These few also represents the core of the PHP community.

Those frameworks have their own properties and usage and they do a pretty great job for most of them. They both have plenty of front and back end features.
Besides those features they have got a lot of tutorials, videos showing how to do this particular thing or this other thing. A lots of trainings also exist helping the community to get bigger everyday.

But hey, we’re all sick of learning how to do this thing in this particular framework way. It is like we have lost a part of simplicity that we needed to understand things swiftly. Differences can be interesting I mean, really, but sometimes the particularity of few framework makes us angry and we could lost time.

### That is why we created Walrus.

It is simple, we wanted it to respect KISS principle, you just have to focus on what matters for your client, and we handle the rest.

It is lightweight, we added only what you really need to build a simple web application. We also use technologies that are already recognized by the community such as YAML or HAML.

It is fast because it’s a client need. 

### Architecture

* **Walrus**/ - All the files of WalrusFramework. This is where magic happens and where you should look at when you want to contribute.
* **config**/
  * *compiled*/ - All YAML config files are converted to PHP files in this directory.
  * *routes*/ - YAML routes files.
  * *skeleton*/ - YAML skeletons files
  * *config.php* - This is where all your Framework configs go (database, ...).
  * *env.php* - All environment variables.
  * *deploy.php* - Deploy configuration
* **app**/ -  All your app files goes here.
    * *engine*/ - This is the back-end of your application.
      * *controllers*/ -  Your own controllers go here.
      * *models*/ - Your own models go here.
      * *api*/ -  Your own API controllers goes here.
  * *www*/ - This is the front-end of your application, basically the files your browsers will be able to get.
      * *assets*/ -  All your images, javascript and styles files.
      * *index.php* - Entry point of your application.
  * *templates*/ - Your templates here.
  * *helpers*/ -  Helpers directory.
* **vendor**/ -  Your vendors go here, few are already included in order to get Walrus working.
* **tmp**/ - Temporary files.
* **tusk** - Walrus Command Line Interface (CLI).
    
#### Notes :
* To use *Walrus framework* you must have a PHP version >= 5.5.0.
* You need to activate url_rewriting module on your server.
