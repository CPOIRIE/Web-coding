<?php
/*------------------------------------------------------------------- LES FONCTIONS -------------------------------------------------------------------*/ 

/*AFFICHE LES RESULTATS D'UNE RECHERCHE*/ 

/* Requète récupérant les affiches et les titres des séries recherchées */
function pageAffiche(){
    if ($_POST['inputSearch']!= ''){
        $title ='all';

        /*AFFICHE LES RESULTATS CORRESPONDANTS AUX SERIES  */
        if ($_POST['filtre']=='tv'){
            $requestSearch = 'https://api.themoviedb.org/3/search/tv?api_key=f5fb7a294854b93bb56c36d778fb632b&query='.$_POST['inputSearch'].'';
            $SearchResponse = file_get_contents($requestSearch);
            $tabSearch = json_decode($SearchResponse,true);
            $title = 'name';
            $HTML_CODE = generateHtmlAffiche($tabSearch,$title,'tv');

        /*AFFICHE LES RESULTATS CORRESPONDANTS AUX FILMS  */
        }elseif($_POST['filtre']=='movie'){
            $requestSearch = 'https://api.themoviedb.org/3/search/movie?api_key=f5fb7a294854b93bb56c36d778fb632b&query='.$_POST['inputSearch'].'';
            $SearchResponse = file_get_contents($requestSearch);
            $tabSearch = json_decode($SearchResponse,true);
            $title = 'title';
            $HTML_CODE = generateHtmlAffiche($tabSearch,$title,'movie');
        
        /*AFFICHE LES RESULTATS CORRESPONDANTS AUX DEUX CATEGORIES  */
        }elseif($_POST['filtre']=='all'){

            $requestSearch = 'https://api.themoviedb.org/3/search/movie?api_key=f5fb7a294854b93bb56c36d778fb632b&query='.$_POST['inputSearch'].'';
            $SearchResponse = file_get_contents($requestSearch);
            $tabSearchMovie = json_decode($SearchResponse,true);

            $HTML_CODE = generateHtmlAffiche($tabSearchMovie,'title','movie');

            $requestSearch = 'https://api.themoviedb.org/3/search/tv?api_key=f5fb7a294854b93bb56c36d778fb632b&query='.$_POST['inputSearch'].'';
            $SearchResponse = file_get_contents($requestSearch);
            $tabSearchTV = json_decode($SearchResponse,true);
            
            $HTML_CODE .= generateHtmlAffiche($tabSearchTV,'name','tv');

        }
        
        echo($HTML_CODE);
    }  
}

/* AFFICHE LES TENDENCES ACTUELLES*/
/* Possibilité de filtrer film ou serie grace a la variable filtre */

function AfficherTendences(){
    $requestSearch = 'https://api.themoviedb.org/3/trending/'.$_POST['filtre'].'/day?api_key=f5fb7a294854b93bb56c36d778fb632b';
    $SearchResponse = file_get_contents($requestSearch);
    $tabSearch = json_decode($SearchResponse,true);
    $HTML_CODE = '';

    if ($tabSearch['total_results']>0){
        for ($i=0; $i< count($tabSearch['results']); $i++){
            if($tabSearch['results'][$i]['poster_path']!= null){

                /* Si c'est un film le champ pour récupérer le titre se nomme 'title et non 'name' */
                if($tabSearch['results'][$i]['media_type']== 'movie'){
                    $title='title';
                    $class = "wrapMovie";
                }
                if($tabSearch['results'][$i]['media_type']== 'tv'){
                    $title='name';
                    $class = "wrapShow";
                }

                $id = $tabSearch['results'][$i]['id'];
                $poster = $tabSearch['results'][$i]['poster_path'];
               /*  echo '<div id="wrapAffiche"'; */
               $HTML_CODE .= '<div class="'. $class.'" id = '.$id.'>';
               $HTML_CODE.= '<img class ="imgShow" src="http://image.tmdb.org/t/p/w500'.$poster.'" alt="">';
               $HTML_CODE.= '<p class = "titreAffiche">'.$tabSearch['results'][$i][ $title].'</p>';
               $HTML_CODE.= '</div>';
            /*     echo '</div>'; */
            }
        }
    }else{
        $HTML_CODE .= '<p>Error: no results matching your request. Please try again.<p>';
    }
    echo($HTML_CODE);
}  

/*AFFICHE LES DETAILS SUR UNE SERIE*/

function pageDetail(){

    $requestSearch = 'https://api.themoviedb.org/3/tv/'.$_POST['id'].'?api_key=f5fb7a294854b93bb56c36d778fb632b';
    $SearchResponse = file_get_contents($requestSearch);
    $tabSearch = json_decode($SearchResponse,true);

    $HTML_CODE="";
    /* DIV wrapper */
    $HTML_CODE .= '<input type="hidden" id = "idShow" value="'.$_POST['id'].'">';
    $HTML_CODE .=('<div id="wrapper">');

        /* DIV LEFT */
        $HTML_CODE .= '<div id = "left">';
        $HTML_CODE .= '<img id ="MainImgDetail" src="http://image.tmdb.org/t/p/w500'.$tabSearch['poster_path'].'" alt="">';
        $HTML_CODE .= '</div>';

        /* FIN LEFT */

        /* DIV  right*/
        $HTML_CODE .= '<div id="right">';

            /* DIV topRight */
            $HTML_CODE .= '<div id="topRight">';
                
            $HTML_CODE .= ('<h1>'.$tabSearch['name'].'</h1><br>');
            $HTML_CODE .=('<p><strong> Created by:</strong> ');

                
                /* CREATEURS */
                for($i=0;$i<count($tabSearch['created_by']);$i++){
                    /* Si ce n'est pas le dernier element */
                    if ($i != (count ( $tabSearch['created_by'] ) -1)){
                        $HTML_CODE .= ($tabSearch['created_by'][$i]['name'].', ');
                    }
                    /* Pour le dernier element nous meetons un point */
                    else{
                        $HTML_CODE .= ($tabSearch['created_by'][$i]['name'].'.');
                    } 
                };
                /* GENRES */

                $HTML_CODE .=('<br><p><strong> Genres:</strong> ');
                for($i=0;$i<count($tabSearch['genres']);$i++){
                    /* Si ce n'est pas le dernier element */
                    if ($i != (count ( $tabSearch['genres'] ) -1)){
                        $HTML_CODE .= ($tabSearch['genres'][$i]['name'].', ');
                    }
                    /* Pour le dernier element nous meetons un point */
                    else{
                        $HTML_CODE .= ($tabSearch['genres'][$i]['name'].'.');
                    } 
                };

                /* NB EPISODES/ SEASONS */
                $HTML_CODE .= ('<p><strong>Number of seasons:</strong> '.$tabSearch['number_of_seasons'].'.<br>');
                $HTML_CODE .= ('<p><strong>Number of episodes:</strong> '.$tabSearch['number_of_episodes'].'.<br>');
                $HTML_CODE .= ('<p><strong>Overview:</strong> '.$tabSearch['overview'].'.<br>');


                
                $adresseTrailer = videoTrailer();
                
                if ($adresseTrailer != "empty"){
                    $HTML_CODE .=' <form method=get action="http://www.google.fr/search" target="_blank">';
                        $HTML_CODE .= '<input type=hidden name=q size=30 maxlength=255 value= "'.$adresseTrailer['results'][0]['name'].'">';
                        $HTML_CODE .= '<input type=hidden name=hl value=fr>';
                        $HTML_CODE .= '<input type=submit id = "boutonTrailer" name=btnG value="Watch '.$adresseTrailer['results'][0]['type'].'">';
                        $HTML_CODE .= '</form>';
                    
                }
                $HTML_CODE .= '</div>';
            /* FIN DIV topRight */

            /* DIV bottomRight */
            $HTML_CODE .= '<div id="bottomRight">';
                    /* DIV navSeasons */
                    $HTML_CODE .= '<div id="navSeasons">';
                        for($j=0;$j<count($tabSearch['seasons']);$j++){
                                $HTML_CODE .= '<a href="#detailSaison"><input id = "'.$j.'"class = "buttonSeasonChose" type="button" value = "'.$tabSearch['seasons'][$j]['name'].'"></a>';
                        }
                        $HTML_CODE .= '</div>';
                    /* FIN DIV navSeasons */
                    /* DIV SeasonDetail */                        
                    /* FIN DIV SeasonDetail */


                    $HTML_CODE .= '</div>';
            /* FIN DIV bottomRight */

        /* FIN DIV RIGHT */
        $HTML_CODE .= '</div>';
    /* FIN WRAPPER  */    
    $HTML_CODE .= '</div>';

    echo ($HTML_CODE);
    
}

/*RENVOIE LE LIEN VERS LE TRAILER*/

function videoTrailer(){
    $requestSearch = 'https://api.themoviedb.org/3/tv/'.$_POST['id'].'/videos?api_key=f5fb7a294854b93bb56c36d778fb632b';
    $SearchResponse = file_get_contents($requestSearch);
    $tabSearch = json_decode($SearchResponse,true);

    if (empty($tabSearch['results'][0])){
         return('empty');
    }else{
        return ($tabSearch);
    }

}

/*RENVOIE LE CODE HTML AVEC LE TITRE ET LE RESUME DE LA SAISON SELECTIONNEE*/

function detailSaison(){

    /* $id = $_POST['idNumSeason'] + 1; /* Pas de saison 0 */ 
    $idSeason = $_POST['idNumSeason'];
    $HTML_CODE = "";

    $requestSearch = 'https://api.themoviedb.org/3/tv/'.$_POST['id'].'?api_key=f5fb7a294854b93bb56c36d778fb632b';
    $SearchResponse = file_get_contents($requestSearch);
    $tabSearch = json_decode($SearchResponse,true);

    $HTML_CODE .= '<h2>'.$tabSearch["seasons"][$idSeason]['name'].'</h2>';

    if(($tabSearch["seasons"][$idSeason]['overview'])!= ""){
        $HTML_CODE .= '<p>'.$tabSearch["seasons"][$idSeason]['overview'].'</p>';
    }else{
        $HTML_CODE .= '<p>Sorry, the overview is currently unavailable.</p>';
    }
    $HTML_CODE .= '<div id = "episodesDetail">';
    $HTML_CODE .= '<input id = '.$tabSearch['seasons'][$idSeason]['season_number'].' class="buttonEpisode" type="button" value = "Show the episode list">';
    $HTML_CODE .= '</div>';

    echo $HTML_CODE;
}

/*RENVOIE UN TABLEAU HTML CONTENANT LES RESUMEES IMAGE ET TITRE DES EPISODE DE LA SAISON SELECTIONEE*/

function detailEpisodes(){
    $idSeason = $_POST['idNumSeason'];
    $HTML_CODE ="";
    $requestSearch = 'https://api.themoviedb.org/3/tv/'.$_POST['id'].'/season/'.$idSeason.'?api_key=f5fb7a294854b93bb56c36d778fb632b';
    $SearchResponse = file_get_contents($requestSearch);
    $tabSearch = json_decode($SearchResponse,true);

    $HTML_CODE .= '<table>';
    for ($i=0; $i<count($tabSearch['episodes']);$i++){

        $HTML_CODE .= '<tr> <td> <h2>'.($tabSearch['episodes'][$i]['name']).' </h2><br> <img class ="ImgEpisode" src="http://image.tmdb.org/t/p/w500'.$tabSearch['episodes'][$i]['still_path'].'" alt=""> </td> <td>  '.($tabSearch['episodes'][$i]['overview']).'  </td> </tr>';

    }
    $HTML_CODE .= '</table>';
    echo $HTML_CODE;
}

/*GENERATION DE CODE HTML*/

function generateHtmlAffiche($tabParam,$titleParam,$typeParam){
    $HTML_CODE = '';
    if ($tabParam['total_results']>0){
        for ($i=0; $i< count($tabParam['results']); $i++){

            if($tabParam['results'][$i]['poster_path']!= null){
                if ($typeParam== 'tv'){
                    $class = 'wrapShow';
                }elseif($typeParam=='movie'){
                    $class = 'wrapMovie';
                }
                $id = $tabParam['results'][$i]['id'];
                $poster = $tabParam['results'][$i]['poster_path'];
               $HTML_CODE .= '<div class="'.$class.'" id = '.$id.'>';
               $HTML_CODE.= '<img class ="imgShow" src="http://image.tmdb.org/t/p/w500'.$poster.'" alt="">';
               $HTML_CODE.= '<p class = "titreAffiche">'.$tabParam['results'][$i][$titleParam].'</p>';
               $HTML_CODE.= '</div>';
            }
        }
    }else{
        $HTML_CODE .= '<p>Error: no results matching your request. Please try again.<p>';
    }
    return($HTML_CODE);

}

/*------------------------------------------------------------------- FONCTION PRINCIPALE -------------------------------------------------------------------*/  
  
function requete($requeteParam){

        switch ($requeteParam) {
            case 'pageAffiche':
                pageAffiche();
            break;
            case 'pageDetail':
                pageDetail();
            break;
            case 'detailSaison':
                detailSaison();
            break;
            case 'detailEpisode':
                detailEpisodes();
            break;
            case 'AfficherTendences' :
                AfficherTendences();
            break;
        }
} 

/*-------------------------------------------------------------------MAIN-------------------------------------------------------------------*/  

requete($_POST['requeteType']);


?>
