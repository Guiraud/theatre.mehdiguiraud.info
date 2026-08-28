# Accessibilité Brevo, contenus légaux et pied de page — conception

## Objectif

Corriger les noms accessibles des champs du formulaire Brevo, publier des pages
WordPress dédiées aux mentions légales et à la confidentialité, puis enrichir le
pied de page avec ces liens et un accès stable au formulaire d'inscription.

## Architecture

Le thème conserve une séparation nette entre contenu et présentation :

- les textes légaux vivent dans des pages WordPress éditables ;
- le thème résout leurs URL et construit le pied de page ;
- une fonction du thème répare le balisage accessible produit par Brevo après
  l'exécution du shortcode ;
- le formulaire newsletter reçoit l'ancre stable `#recevoir-les-pieces`.

### Accessibilité du formulaire Brevo

Brevo 3.3.5 stocke des éléments `<label>` dans le formulaire par défaut, mais les
supprime lors du rendu : sa liste blanche `SIB_ATTRIBUTE` autorise les champs sans
autoriser `label`. Le thème traite donc le résultat de `do_shortcode()` avant de
l'afficher.

Pour chaque champ visible sans label associé, le thème :

1. conserve le texte visible fourni par le formulaire ;
2. attribue au champ un identifiant stable et unique ;
3. restaure un véritable `<label for="…">` ;
4. laisse intacts les champs cachés, boutons et contrôles techniques.

La transformation est idempotente. Si une future version de Brevo conserve déjà
des labels corrects, le thème n'ajoute rien. Si le balisage est inconnu, il reste
inchangé afin de ne pas empêcher l'inscription.

## Pages légales

### Mentions légales

Une page WordPress dédiée présente :

- l'édition personnelle et non professionnelle du site ;
- Mehdi Guiraud comme directeur de la publication ;
- `fred2baro+dataofficer@gmail.com` comme contact public ;
- l'identité et les coordonnées officielles vérifiées de l'hébergeur OVH ;
- des dispositions sobres sur la propriété intellectuelle, les demandes de
  correction et le signalement de contenu.

L'adresse personnelle et le numéro de téléphone de l'éditeur ne sont pas publiés.
Le régime de l'éditeur non professionnel et les informations attendues sont
documentés par l'article 6 de la LCEN :
<https://www.legifrance.gouv.fr/codes/article_lc/LEGIARTI000044067469/2024-01-26>.

### Confidentialité

La page WordPress existante « Confidentialité — Newsletter », déjà déclarée comme
politique de confidentialité, devient « Confidentialité ». Elle est complétée à
partir d'un inventaire des traitements réellement actifs sur le site.

Elle décrit au minimum :

- le responsable du traitement et son contact ;
- les finalités et bases légales ;
- l'adresse électronique obligatoire, le prénom et le nom facultatifs du
  formulaire Brevo ;
- la source des données, les destinataires, les transferts éventuels et les
  durées de conservation ;
- la désinscription, l'absence de décision automatisée et les droits des
  personnes ;
- le droit de saisir la CNIL.

L'inventaire préalable couvre également le formulaire de contact, les
commentaires, les cookies, les journaux de sécurité et les statistiques. Aucun
traitement non observé n'est inventé. Référence CNIL :
<https://www.cnil.fr/fr/respecter-les-droits-des-personnes>.

## Pied de page

Le pied de page devient une grille responsive en trois zones :

1. identité éditoriale actuelle ;
2. navigation légale ;
3. appel à l'action « Recevoir les pièces ».

Les URL sont obtenues par les API WordPress :

- `get_privacy_policy_url()` pour la confidentialité ;
- une page sélectionnée dans le personnalisateur pour les mentions légales ;
- `home_url( '/#recevoir-les-pieces' )` pour la newsletter.

Une page légale absente, supprimée ou non publiée ne produit pas de lien mort. Le
menu de pied de page et la zone de widgets existants restent compatibles.

L'ancre newsletter fonctionne depuis toutes les pages. Son décalage de défilement
évite qu'elle soit masquée par le bandeau de navigation.

## Responsive et accessibilité

Le pied de page utilise la charte existante : fond noir, typographie éditoriale,
rouge pour les liens et focus visible. La grille se replie verticalement sur petit
écran. Les cibles interactives respectent une hauteur minimale de 44 px sans
introduire de débordement horizontal.

La recette visuelle couvre 375, 768, 1024 et 1440 px. La navigation au clavier,
les états de focus, les noms accessibles des champs et les erreurs console sont
contrôlés dans un navigateur réel.

## Replis et déploiement

- Formulaire Brevo inconnu ou shortcode absent : le repli iframe actuel reste
  inchangé.
- HTML Brevo non reconnu : aucune transformation destructive.
- Page légale absente : aucun lien correspondant.
- Page de confidentialité existante : elle reste publiée pendant la préparation.

Les réglages et pages WordPress sont sauvegardés avant modification. Le code suit
trois cycles TDD distincts : accessibilité Brevo, rendu du pied de page, ancre et
URL. Après lint PHP, tests complets et reconstruction de l'archive, les fichiers et
contenus sont déployés, WP Super Cache est purgé, puis la page publique sans
cache-buster est contrôlée.
