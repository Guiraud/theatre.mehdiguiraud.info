# Brevo Newsletter Embed Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Afficher le formulaire Brevo « Recevoir les pièces » dans le bandeau newsletter du thème WordPress, de façon sûre, accessible et responsive.

**Architecture:** Ajouter un réglage de thème contenant uniquement l’URL publique `https://*.sibforms.com/v2/serve/...`, validée par une fonction dédiée. Le gabarit construit lui-même l’iframe avec des attributs sûrs ; aucun HTML arbitraire fourni par l’administration n’est rendu. Le CSS réserve l’espace et contraint l’iframe à la largeur disponible.

**Tech Stack:** PHP 8.0+, API Customizer WordPress, HTML/CSS classique, script de test PHP autonome.

---

### Task 1: Test de validation et de rendu

**Files:**
- Create: `tests/test-newsletter-embed.php`
- Test: `tests/test-newsletter-embed.php`

1. Écrire un test autonome avec les fonctions WordPress minimales simulées.
2. Vérifier qu’il échoue parce que le réglage et l’iframe Brevo n’existent pas encore.
3. Couvrir une URL Brevo HTTPS valide et le rejet de HTTP, d’un autre domaine et d’un autre chemin.
4. Couvrir le rendu accessible et responsive de l’iframe.

### Task 2: Réglage sécurisé et gabarit

**Files:**
- Modify: `inc/customizer.php`
- Modify: `template-parts/lettre.php`
- Test: `tests/test-newsletter-embed.php`

1. Ajouter `refoulement_sanitize_brevo_embed_url()`.
2. Ajouter le réglage `refoulement_lettre_brevo_url` de type URL Brevo.
3. Donner priorité au formulaire filtré, puis à l’iframe Brevo, puis au formulaire générique et enfin au courriel de repli.
4. Construire l’iframe avec `title`, `loading="lazy"`, dimensions réservées et URL échappée.
5. Relancer le test jusqu’au vert.

### Task 3: Responsive, documentation et paquet

**Files:**
- Modify: `assets/css/theme.css`
- Modify: `README.md`
- Modify: `tests/test-newsletter-embed.php`

1. Ajouter les styles de largeur, hauteur et bordure de l’iframe.
2. Documenter le réglage Brevo.
3. Vérifier le test PHP, la syntaxe de tous les fichiers PHP et reconstruire l’archive.

### Task 4: Déploiement et vérification

**Files:**
- Deploy: `inc/customizer.php`
- Deploy: `template-parts/lettre.php`
- Deploy: `assets/css/theme.css`

1. Copier les trois fichiers sur OVH après sauvegarde distante.
2. Enregistrer l’URL publique du formulaire dans le réglage de thème.
3. Purger le cache WordPress.
4. Vérifier l’accueil en HTTPS, l’iframe et sa ressource publique.
5. Tester l’affichage bureau et mobile avant de déclarer l’intégration terminée.

> Les commits sont différés : le thème repris de la session précédente est presque entièrement non suivi par Git. Un commit partiel mélangerait la base du thème et cette fonctionnalité sans historique fiable.
