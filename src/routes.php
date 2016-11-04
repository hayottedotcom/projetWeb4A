<?php


// Routes
// Route de pré-remplissage de la basse données
$app->get('/install', function ($request, $response, $args) {
    $this->db;
    $capsule = new \Illuminate\Database\Capsule\Manager;
    //Création de la table articles avec ses paramètres
    $capsule::schema()->dropIfExists('articles');
    $capsule::schema()->create('articles', function (\Illuminate\Database\Schema\Blueprint $table) {
    
        $table->increments('id');
        $table->string('name');
	    $table->string('recette');
	    $table->string('createur');
	    $table->string('region');
	    $table->string('description');
        $table->string('img');
    

	
        // Include created_at and updated_at
        $table->timestamps();
    });
    
    //Tableau de pré-remplissage
    $specialitesNom= array("Quiche Lorraine","Baeckeoffe");
    $specialitesDes= array("Elle a fait les delices de la cours de Nancy des 1550","Plat traditionnel de la cuisine Alsacienne");
    $specialitesRecette=array("-200g de lardon,-200g de pâte brisée,...","-500g d épaule d agneau, -25g de saindoux,...");
    $specialitesImg= array("quicheLorraine.jpg","baeckeoffe.jpg");
    $specialitesCrea= array("Florent","Florent");
    $specialitesReg= array("Lorraine", "Alsace");

    //Boucle d'initialisation des variables pointant vers le modèle (BDD)
    for($i=0;$i<=1;$i++){
	${$i.$Articles}=new Articles;
        ${$i.$Articles}->name=$specialitesNom[$i];
	${$i.$Articles}->description=$specialitesDes[$i];
        ${$i.$Articles}->img=$specialitesImg[$i];
        ${$i.$Articles}->createur=$specialitesCrea[$i];
	${$i.$Articles}->recette=$specialitesRecette[$i];
        ${$i.$Articles}->region=$specialitesReg[$i];

        ${$i.$Articles}->save();
    }
    
    //Renvoi vers la page d'accueil
    return $response->withStatus(302)->withHeader("Location","/");
});

//Route récupérant un post du formulaire d'ajout de recettes
$app->post('/upload', function ($request, $response, $args) {

    //On récupère les paramètres du formulaire
    $request->getQueryParams();
    $request->getParams();
    $pseudo=$request->getParam('typePseudo');
    $name=$request->getParam('typeName');
    $region=$request->getParam('choiseRegion');
    $description=$request->getParam('typeDesc');
    $recette=$request->getParam('typeR');

    $this->db;
    
    //On remplit la table avec les nouveaux paramètres
    $articles=new Articles;
    $articles->name=$name;
    $articles->createur=$pseudo;
    $articles->recette=$recette;
    $articles->region=$region;
    $articles->description=$description;
    
    //On récupère le fichier, on l'upload dans le dossier public et on ajoute le nom dans la BDD
    $files = $request->getUploadedFiles();
    if (empty($files['newfile'])) {
        throw new Exception('Pas de fichier upload');
    }
 
    $newfile = $files['newfile'];
    if ($newfile->getError() === UPLOAD_ERR_OK) {
    $uploadFileName = $newfile->getClientFilename();
    $newfile->moveTo("public/img/upload/$uploadFileName");

    $articles->img=$uploadFileName;
    $articles->save();
    
    //Renvoi vers la page d'accueil
    return $response->withStatus(302)->withHeader("Location","/");
}	
});


//route redirigeant vers la page d'acceuil
$app->get('/', function ($request, $response, $args) {

    //On récupère les éléments de la base de données grâce au modèle, puis on envoie le tableau à l'index.html
    $this->db;
    $articles=Articles::all();
    
    return $this->renderer->render($response, 'index.phtml',['articles'=> $articles]);
});

//route récupérant un GET d'un bouton pour supprimer un article avec l'id en paramètre
$app->get('/delete/[{id}]', function ($request, $response, $args) {
    
    $this->db;
    
    //On supprime la ligne correspondant à l'ID
    $delete=Articles::where('id',$args['id'])->delete();
    
    //Renvoi vers la page d'accueil
    return $response->withStatus(302)->withHeader("Location","/");

});

//route récupérant un POST d'un formulaire pour modifier un article
$app->post('/modif/[{id}]', function ($request, $response, $args) {
    
    $this->db;
    //On récupère les paramètres du formulaire
    $request->getQueryParams();
    $request->getParams(); 
    $name=$request->getParam('typeName');
    $region=$request->getParam('choiseRegion');
    $description=$request->getParam('typeDesc');
    $recette=$request->getParam('typeR');

    $files = $request->getUploadedFiles();
    $articles=Articles::all();
    $uploadFileName=$articles[$args['id']-1]->img;
    //On récupère le fichier, on l'upload dans le dossier public et on ajoute le nom dans la BDD
    $newfile = $files['newfile'];
    if ($newfile->getError() === UPLOAD_ERR_OK) {
    $uploadFileName = $newfile->getClientFilename();
    $newfile->moveTo("public/img/upload/$uploadFileName");
    }
    
    //On modifie la ligne avec les nouveaux paramètres correspondant à l'ID
    $modif=Articles::where('id',$args['id'])->update(['description'=>$description]);
    $modif=Articles::where('id',$args['id'])->update(['name'=>$name]);
    $modif=Articles::where('id',$args['id'])->update(['recette'=>$recette]);
    $modif=Articles::where('id',$args['id'])->update(['region'=>$region]);
    $modif=Articles::where('id',$args['id'])->update(['img'=>$uploadFileName]);

    //Renvoi vers la page d'accueil
    return $response->withStatus(302)->withHeader("Location","/");

});

