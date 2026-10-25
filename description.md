# NsListModExterne — Inventaire des modules externes de Dolibarr

Fiche de présentation du module pour **Dolistore.com** (version française puis version anglaise).

---

# 🇫🇷 Version française

## Nom du module

**NsListModExterne – Inventaire des modules externes de Dolibarr**

## Description courte

Inventaire de tous les modules externes installés sur votre Dolibarr dans un fichier JSON : version, éditeur, numéro, famille, actif ou non, compatibilité, dépendances. Régénéré chaque jour, téléchargeable directement depuis les réglages du module, à archiver ou à transmettre à votre prestataire.

## Description longue

### Quels modules sont installés, et en quelle version ?

Au fil des années, une instance Dolibarr accumule des modules externes : achetés sur le Dolistore, développés sur mesure, installés pour un essai puis oubliés… Avant une mise à jour de Dolibarr, une migration ou un audit, il faut savoir précisément ce qui est en place.

**NsListModExterne** fait cet inventaire pour vous : il recense **tous les modules externes** de l'instance, activés ou non, dans un **fichier JSON** à télécharger.

### Un fichier JSON complet

- Pour chaque module : **nom**, **libellé**, **description**, **version**, **éditeur**, **numéro de module**, **famille** et **état actif ou inactif**.
- Également : date d'activation, dossier d'installation, versions minimales de Dolibarr et de PHP, dépendances, modules requis et incompatibles, page de réglages, fichiers de langue.
- Les **fonctionnalités** de chaque module : nombre de permissions, de menus, d'onglets, de dictionnaires, de widgets et de tâches planifiées, triggers, modèles de documents, hooks, CSS et JS.
- L'URL de l'instance, l'entité, la version de Dolibarr, la version de PHP, le nombre de modules externes et le nombre de modules activés.
- Seuls les modules externes sont recensés : les modules standard de Dolibarr sont écartés.

### Toujours à jour

- Un bouton **Générer le fichier JSON**, dans les réglages du module, le régénère à la demande.
- Une **tâche planifiée quotidienne** le régénère automatiquement.
- La date de dernière génération est affichée dans les réglages, avec le bouton **Télécharger**.

### Un format ouvert, facile à exploiter

- Le format JSON se lit avec n'importe quel outil : tableur, script, outil de suivi de parc…
- Il devient facile de comparer plusieurs instances, de vérifier la présence ou la version d'un module, ou de préparer une mise à jour.
- Avec le module **NsSuiviProd** (« Suivi des sites ») de votre prestataire, le fichier s'importe en un clic : la liste des plugins du site, leurs versions et leurs versions sur le Dolistore sont mises à jour.

### Simple et sûr

- Pas de menu ni de page supplémentaire : tout se passe dans les réglages du module.
- Option pour **masquer le module** dans la liste des modules de Dolibarr : il reste actif, ses réglages restent accessibles par leur adresse.
- Compatible **multisociété** : un fichier par entité.
- **Aucun prérequis** et **aucune modification du Core** de Dolibarr.

## Points forts

- ✅ Inventaire complet des modules externes dans un fichier JSON
- ✅ Version, éditeur, numéro, famille, compatibilité et état actif / inactif de chaque module
- ✅ Téléchargeable directement depuis les réglages du module
- ✅ Régénération quotidienne automatique ou à la demande
- ✅ Format JSON ouvert, exploitable hors de Dolibarr et importable dans NsSuiviProd
- ✅ Sans modification du Core, sans autre module à installer

## Compatibilité

- Dolibarr **14.0 à 24.0** (testé sur Dolibarr 23.0)
- PHP **7.4 à 8.3** (testé en PHP 8.3)
- **Aucun prérequis** : module autonome, sans autre module à installer
- Langues : français, anglais

## Mots-clés

modules, modules externes, inventaire, liste des modules, version, éditeur, numéro de module, compatibilité, dépendances, JSON, export, audit, maintenance, administration, outils, NSINFO

---

# 🇬🇧 English version

## Module name

**NsListModExterne – Inventory of the Dolibarr external modules**

## Short description

Inventory of all the external modules installed on your Dolibarr in a JSON file: version, publisher, number, family, enabled or not, compatibility, dependencies. Regenerated every day, downloadable directly from the module setup, to archive it or send it to your service provider.

## Long description

### Which modules are installed, and in which version?

Over the years, a Dolibarr instance piles up external modules: bought on the Dolistore, custom-developed, installed for a test and then forgotten… Before a Dolibarr upgrade, a migration or an audit, you need to know exactly what is in place.

**NsListModExterne** takes this inventory for you: it lists **all the external modules** of the instance, enabled or not, in a **JSON file** to download.

### A complete JSON file

- For each module: **name**, **label**, **description**, **version**, **publisher**, **module number**, **family** and **enabled or disabled status**.
- Also: activation date, installation folder, minimum Dolibarr and PHP versions, dependencies, required and conflicting modules, setup page, language files.
- The **features** of each module: number of permissions, menus, tabs, dictionaries, widgets and scheduled jobs, triggers, document models, hooks, CSS and JS.
- The URL of the instance, the entity, the Dolibarr version, the PHP version, the number of external modules and the number of enabled ones.
- Only external modules are listed: the standard Dolibarr modules are left out.

### Always up to date

- A **Generate the JSON file** button, in the module setup, regenerates it on demand.
- A **daily scheduled job** regenerates it automatically.
- The date of the last generation is shown in the setup, with the **Download** button.

### An open format, easy to use

- The JSON format can be read by any tool: spreadsheet, script, asset management tool…
- Comparing several instances, checking whether a module is installed or its version, or preparing an upgrade becomes easy.
- With the **NsSuiviProd** ("Site tracking") module of your service provider, the file is imported in one click: the list of the plugins of the site, their versions and their Dolistore versions are updated.

### Simple and safe

- No extra menu or page: everything happens in the module setup.
- Option to **hide the module** in the list of the Dolibarr modules: it stays enabled, its setup is still available at its address.
- **Multicompany** compatible: one file per entity.
- **No requirement** and **no change to the Dolibarr Core**.

## Highlights

- ✅ Complete inventory of the external modules in a JSON file
- ✅ Version, publisher, number, family, compatibility and enabled / disabled status of each module
- ✅ Downloadable directly from the module setup
- ✅ Automatic daily regeneration, or on demand
- ✅ Open JSON format, usable outside Dolibarr and importable into NsSuiviProd
- ✅ No change to the Core, no other module to install

## Compatibility

- Dolibarr **14.0 to 24.0** (tested on Dolibarr 23.0)
- PHP **7.4 to 8.3** (tested with PHP 8.3)
- **No requirement**: standalone module, no other module to install
- Languages: French, English

## Keywords

modules, external modules, inventory, module list, version, publisher, module number, compatibility, dependencies, JSON, export, audit, maintenance, administration, tools, NSINFO
