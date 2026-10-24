# Conformité Meta (Facebook Developers) — Pages légales et suppression des données

Ce document décrit les éléments exigés par Meta pour passer l'application en mode « Publiée » ou la soumettre en revue, ainsi que les contenus des pages à créer dans WordPress.

---

## 1. Récapitulatif des champs Meta Dashboard

Dans votre espace **Meta for Developers** > **Paramètres** > **De base** :

| Champ Meta | Option / Type | URL à renseigner |
| :--- | :--- | :--- |
| **URL de la politique de confidentialité** | HTTPS valide | `https://theatre.mehdiguiraud.info/index.php/confidentialite/` |
| **URL des conditions de service** | HTTPS valide | `https://theatre.mehdiguiraud.info/index.php/conditions-de-service/` |
| **Suppression des données des utilisateurs** | Sélectionner : *URL des instructions de suppression des données* | `https://theatre.mehdiguiraud.info/index.php/suppression-des-donnees/` |

---

## 2. État de la Politique de confidentialité

- **URL testée :** `https://theatre.mehdiguiraud.info/index.php/confidentialite/`
- **Statut :** Accessible en HTTPS (code HTTP 200).
- **Contenu existant :** Couvre le responsable du traitement, la newsletter Brevo, les commentaires, la sécurité/hébergement OVH, les cookies, les droits RGPD et le contact (`fred2baro+dataofficer@gmail.com`).

---

## 3. Contenus des nouvelles pages à publier sur WordPress

Dans votre administration WordPress (**Pages** > **Ajouter une page**) :

### A. Page « Conditions de service »

- **Titre de la page :** `Conditions de service`
- **Identifiant (slug) :** `conditions-de-service`
- **URL finale :** `https://theatre.mehdiguiraud.info/index.php/conditions-de-service/`

**Contenu à coller dans l'éditeur de texte / bloc :**

```html
<h2>1. Objet</h2>
<p>Les présentes Conditions de service régissent l’accès et l’utilisation du site « Le projet de théâtre » (accessible à l’adresse <a href="https://theatre.mehdiguiraud.info">theatre.mehdiguiraud.info</a>), ainsi que les fonctionnalités et services associés (notamment la consultation des articles, entretiens, podcasts, l’abonnement à la lettre d’information et les éventuelles connexions tierces).</p>

<h2>2. Acceptation des conditions</h2>
<p>En accédant au site ou en utilisant les services proposés, l'utilisateur accepte sans réserve les présentes conditions. Si vous n’acceptez pas ces termes, vous êtes invité à ne pas utiliser le site.</p>

<h2>3. Propriété intellectuelle</h2>
<p>L’ensemble des contenus originaux (textes, créations graphiques, montages sonores, structures éditoriales) est protégé par le droit de la propriété intellectuelle. Toute reproduction, diffusion ou réutilisation non autorisée est interdite sans accord écrit préalable de l’éditeur.</p>

<h2>4. Services tiers et connexions</h2>
<p>Le site peut proposer des liens, intégrations ou fonctionnalités liées à des services tiers (plateformes de diffusion podcast, services d'envoi d'emails, réseaux sociaux et API Meta / Facebook). L'utilisation de ces services reste soumise aux conditions d'utilisation et politiques respectives de leurs éditeurs.</p>

<h2>5. Responsabilité et disponibilité</h2>
<p>L’éditeur s’efforce de maintenir le site accessible en continu et à jour, mais ne saurait être tenu responsable d’éventuelles interruptions techniques, dysfonctionnements de réseaux tiers ou indisponibilités temporaires.</p>

<h2>6. Contact</h2>
<p>Pour toute question relative aux présentes conditions, vous pouvez contacter l’éditeur à l'adresse : <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a>.</p>
```

---

### B. Page « Suppression des données » (Instructions Meta)

- **Titre de la page :** `Suppression des données utilisateur`
- **Identifiant (slug) :** `suppression-des-donnees`
- **URL finale :** `https://theatre.mehdiguiraud.info/index.php/suppression-des-donnees/`

**Contenu à coller dans l'éditeur de texte / bloc :**

```html
<p>Conformément aux exigences de la plateforme Meta (Facebook) et au Règlement Général sur la Protection des Données (RGPD), vous disposez du droit d'obtenir la suppression intégrale des données associées à votre compte ou à votre utilisation de nos services.</p>

<h2>1. Dissocier l'application depuis votre compte Facebook</h2>
<p>Si vous avez connecté votre compte Facebook à notre service ou application, vous pouvez supprimer l'accès et vos autorisations en suivant ces étapes :</p>
<ol>
	<li>Connectez-vous à votre compte Facebook.</li>
	<li>Allez dans <strong>Paramètres et confidentialité</strong>, puis cliquez sur <strong>Paramètres</strong>.</li>
	<li>Dans le menu latéral gauche, cliquez sur <strong>Applications et sites web</strong>.</li>
	<li>Recherchez notre application dans la liste.</li>
	<li>Cliquez sur <strong>Supprimer</strong> à côté du nom de l'application pour révoquer tout accès.</li>
</ol>

<h2>2. Demande de suppression définitive de vos données sur notre site</h2>
<p>Pour demander l'effacement définitif de toutes les données personnelles collectées vous concernant (inscriptions aux courriels, commentaires, données de profil ou identifiants associés) :</p>
<ol>
	<li>Envoyez un courriel à notre délégué / responsable des données à : <a href="mailto:fred2baro+dataofficer@gmail.com">fred2baro+dataofficer@gmail.com</a>.</li>
	<li>Précisez dans l'objet : <em>« Demande de suppression des données utilisateur »</em>.</li>
	<li>Indiquez dans le message votre identifiant utilisateur Facebook ou l'adresse électronique utilisée.</li>
</ol>
<p>Vos données seront définitivement purgées de nos bases et de nos services sous 30 jours, conformément au RGPD. Une confirmation écrite vous sera adressée par retour de courriel.</p>
```

---

## 4. Ajout au menu de pied de page (Optionnel mais recommandé)

Dans votre administration WordPress :
1. Rendez-vous dans **Apparence** > **Menus**.
2. Sélectionnez le menu affecté au pied de page (**Menu de pied de page**).
3. Cochez les pages **Conditions de service** et **Confidentialité** puis cliquez sur **Ajouter au menu**.
4. Enregistrez les modifications.
