### Test Rest ###

Install bundles:

```sudo composer update```

Install BDD:

```sudo php app/console doctrine:schema:update --force```

Launch tests:

```phpunit -c app src/Demo/AuthRestBundle/Tests/Controller```

Testing by curl, example:

```curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"user":{"email": "user1@test.org"}}' http://localhost/symfonyRest/web/app.php/api/user```

How to use:

Show one user:

  * method: POST
  * url: /api/get/user
  * json: '{"email": (string)}'

Add user:

  * method: POST
  * url: /api/user
  * json: '{"nom": (string), "prenom": (string), "email": (string), "password": (string)}'

Update user:

  * method: PUT
  * url: /api/user
  * json: '{"nom": (string), "prenom": (string), "email": (string), "password": (string)}'

Delete user:

  * method: DELETE
  * url: /api/user
  * json: '{"email": (string)}'


Fields validation:

  * nom: required
  * prenom: required
  * email: required, valid email [UNIQUE INDEX]
  * password: required


