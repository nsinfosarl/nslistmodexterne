# NSLISTMODEXTERNE FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Informations

- Numéro du module : 172792
- Dernière mise à jour : 26/10/2026
- Éditeur : [NSINFO](https://www.ns-info.fr)
- Thème : Eldy Menu
- Licence : GPLv3
- Disponible sur : Windows - MacOS - Linux

### Version

- Version : 23.0.2
- PHP : 7.4 - 8.3
- Compatibilité : Dolibarr 14.0.0 - 24.0.x

## Liens

- Support & Assistance : [Forum dolibarr.fr](https://dolibarr.fr) / Par mail à contact@ns-info.fr
- Support déclaration : [Ticket](https://support.ns-info.fr)
- Documentation : [Wiki](https://www.ns-info.fr/)
- Dépôt GitHub : [nsinfosarl/nslistmodexterne](https://github.com/nsinfosarl/nslistmodexterne)
- D'autres modules développés par NSINFO disponibles sur [Dolistore.com](https://www.dolistore.com/index.php?controller=search&orderby=position&orderway=desc&tag=&website=marketplace&search_query=NSINFO)

## Fonctionnalités

Nslistmodexterne : fichier JSON des modules externes de l'instance, sans aucune modification du Core.

- Fichier JSON nommé d'après l'adresse de l'instance (`documents/nslistmodexterne/<adresse>.json`, exemple `erp.client.fr.json`) : pour chaque module externe, nom, libellé, description, version, éditeur, numéro, famille, état actif ou inactif (`enabled` : true / false, `status` : enabled / disabled), date d'activation, dossier, compatibilité, dépendances, réglages et fonctionnalités
- Le module nslistmodexterne lui-même n'est pas recensé dans le fichier
- Génération et téléchargement depuis la page de réglages du module ; tâche planifiée quotidienne
- Copie écrite en même temps dans `documents/nssuiviprod/` si le module Suivi des sites (`nssuiviprod`) est activé
- Pas de page ni de menu
- Option pour masquer le module dans la liste des modules de Dolibarr (il reste actif ; réglages accessibles par l'adresse `custom/nslistmodexterne/admin/setup.php`)
- Module autonome, sans dépendance au framework NSINFO ; compatible Dolibarr 14 à 24 et PHP 7.4 à 8.3
- Fichier destiné à être téléchargé et exploité hors de l'instance ; la classe `ListModExterne` (`readJsonFile()`, `getModules()`) sert à nos développements

## Traduction

- Français
- Anglais

## Installation

Aucun prérequis : le module est autonome (il ne dépend pas du framework NSINFO).

### Méthode :

- Depuis le menu "Déployer/Installer un module externe" de Dolibarr :
- Glisser l'archive ZIP 'module_nslistmodexterne-X.Y.Z' et cliquer sur "ENVOYER FICHIER"
- Activer le module dans la liste des Modules/Applications installés
