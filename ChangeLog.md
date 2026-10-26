# CHANGELOG NSLISTMODEXTERNE FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## 23.0.2 - 26/10/2026
- Correction de la compatibilité Dolibarr 14 et 15 : isModEnabled() (Dolibarr 16) remplacée par un contrôle de repli dans le descripteur et ajoutée à lib/nslistmodexterne_compat.lib.php ; l'activation ne plante plus

## 23.0.1 - 25/10/2026
- Fichier JSON nommé d'après l'adresse de l'instance Dolibarr (exemple : erp.client.fr.json, « / » et « : » remplacés par « _ »), aussi pour le téléchargement et la copie pour le module Suivi des sites ; l'ancien fichier modules_externes.json est supprimé à la génération suivante

## 23.0.0 - 24/10/2026
- Version initiale (alignement sur les modules NS INFO : famille NS INFO, version dans VERSION)
- Fichier JSON des modules externes de l'instance documents/nslistmodexterne/modules_externes.json (nom, libellé, description, version, éditeur, numéro, famille, état actif / inactif (enabled, status), date d'activation, dossier, compatibilité, dépendances, réglages, fonctionnalités), à télécharger pour être exploité hors de l'instance (classe ListModExterne : getModules(), readJsonFile(), pour nos développements) ; génération et téléchargement depuis la page de réglages du module, tâche planifiée quotidienne ; copie écrite en même temps dans documents/nssuiviprod/ si le module Suivi des sites (nssuiviprod) est activé ; pas de page ni de menu ; droits Consulter / Générer ; numéro 172792
- Module indépendant du framework NSINFO (plus de dépendance au module nsinfo) : compatible Dolibarr 14 à 24 et PHP 7.4 à 8.3 ; fonctions manquantes dans les anciennes versions de Dolibarr intégrées au module (lib/nslistmodexterne_compat.lib.php : dolChmod(), contrôle des droits sans User::hasRight()) ; onglets ChangeLog et À propos propres au module
- Le module nslistmodexterne lui-même n'est pas recensé dans le fichier JSON
- Réglages : icônes en couleur des titres « Fichier JSON des modules externes » et « Paramètres » et du lien de téléchargement, affichées aussi sous Dolibarr 14 (la syntaxe fa-icône_fas_#couleur d'img_picto() n'y existe pas)
- Option des réglages « Masquer le module dans la liste des modules de Dolibarr » (constante NSLISTMODEXTERNE_HIDE_MODULE, propriété hidden du descripteur) : le module reste actif, ses réglages restent accessibles par leur adresse, affichée sous l'option
- Logo : boule NSINFO, badge cubes, texte « Modules externes »
