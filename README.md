# AFFICHE-CSV-BUNDLE

Cette librairie fonctionne avec Symfony <4.4 et php< 7.2
Elle permet l'execution d'une commande afin d'afficher le contenu d'un fichier csv sous forme de tableau.

en effectuant la commande : php bin/console app:csvcommand public\data\products.csv

"app:csvcommand" : nom de la commande

 "public\data\products.csv": qui est l'argument de notre commande  correspond à url de notre fichier csv exemple.
  il suffit juste de modifier l'url pour afficher un autre fichier.
  
  Pour afficher le  contenu du fichier csv au format .Json, ajouter " -- option" à la commande.
  
  exemple: php bin/console app:csvcommand public\data\products.csv --option.
 
