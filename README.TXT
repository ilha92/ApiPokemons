api_php_tp>php -S localhost:8000
:

 POKEMONS
 1. Voir tous les pokémons
bash
Copier
Modifier
curl -i -X GET http://localhost:8000/api_php_tp/src/pokemons
Affiche la liste de tous les pokémons enregistrés dans le fichier CSV.

2. Voir les pokémons page 2 (10 par 10)
bash
Copier
Modifier
curl -i -X GET "http://localhost:8000/api_php_tp/src/pokemons?page=2"
 Montre les pokémons de la page 2 (de 11 à 20 si tu en as assez).

3. Ajouter un nouveau pokémon
bash
Copier
Modifier
curl -i -X POST http://localhost:8000/api_php_tp/src/pokemons -H "Content-Type: application/json" -d "{\"name\": \"Salameche\", \"size\": 55.2}"
Ajoute un pokémon nommé Salameche de taille 55.2 (l'ID sera généré automatiquement).

4. Voir un pokémon par son ID (ex: 1)
bash
Copier
Modifier
curl -i -X GET http://localhost:8000/api_php_tp/src/pokemons/1
Affiche le pokémon qui a l’ID 1.

5. Modifier un pokémon par son ID
bash
Copier
Modifier
curl -i -X PUT http://localhost:8000/api_php_tp/src/pokemons/1 -H "Content-Type: application/json" -d "{\"name\": \"Bulbizarre\", \"size\": 60.0}"
Remplace complètement les infos du pokémon 1 par Bulbizarre avec une nouvelle taille.

6. Supprimer un pokémon par son ID
bash
Copier
Modifier
curl -i -X DELETE http://localhost:8000/api_php_tp/src/pokemons/1
Supprime le pokémon qui a l’ID 1.

UTILISATEURS
7. Voir tous les utilisateurs
bash
Copier
Modifier
curl -i -X GET http://localhost:8000/api_php_tp/src/users
Affiche tous les utilisateurs enregistrés.

8. Créer un nouvel utilisateur
bash
Copier
Modifier
curl -i -X POST http://localhost:8000/api_php_tp/src/users -H "Content-Type: application/json" -d "{\"email\":\"sacha@kanto.com\", \"password\":\"pikachu\", \"name\":\"Sacha\", \"age\":15}"
Ajoute un nouvel utilisateur avec son mail, mot de passe, nom et âge.

9. Voir un utilisateur par son ID
bash
Copier
Modifier
curl -i -X GET http://localhost:8000/api_php_tp/src/users/1
Montre l’utilisateur qui a l’ID 1.

10. Changer le mot de passe d’un utilisateur
bash
Copier
Modifier
curl -i -X PATCH http://localhost:8000/api_php_tp/src/users/1/password -H "Content-Type: application/json" -d "{\"password\":\"newpass123\"}"
Met à jour le mot de passe de l’utilisateur 1.

11. Supprimer un utilisateur
bash
Copier
Modifier
curl -i -X DELETE http://localhost:8000/api_php_tp/src/users/1
Supprime l’utilisateur qui a l’ID 1.

LIENS entre utilisateurs et pokémons (équipes)
12. Ajouter un pokémon à l’équipe d’un utilisateur
bash
Copier
Modifier
curl -i -X POST http://localhost:8000/api_php_tp/src/users/1/pokemons/25
Ajoute le pokémon 25 à l’équipe de l’utilisateur 1 (s’il est mineur : max 4).

13. Retirer un pokémon de l’équipe d’un utilisateur
bash
Copier
Modifier
curl -i -X DELETE http://localhost:8000/api_php_tp/src/users/1/pokemons/25
Retire le pokémon 25 de l’équipe de l’utilisateur 1.

14. Voir les pokémons d’un utilisateur
bash
Copier
Modifier
curl -i -X GET http://localhost:8000/api_php_tp/src/users/1/pokemons
Montre toute l’équipe de pokémons de l’utilisateur 1.

