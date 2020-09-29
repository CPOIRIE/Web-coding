/*-------------------------------------------------------------------LES FONCTIONS-------------------------------------------------------------------  */

function afficherTendances(){
    $id = $(this).attr('id');

    switch ($id) {
        case 'all':
            filtre='all';
            $('#titre').text('TOP OF THE DAY')
            $('#tvShows').css('text-decoration','none');
            $('#movies').css('text-decoration','none');
        break;

        case 'movies':
            filtre='movie';
            $('#titre').text('TOP MOVIES OF THE DAY')
            $('#tvShows').css('text-decoration','none');
            $('#all').css('text-decoration','none');
        break;
        case 'tvShows':
            filtre='tv';
            $('#titre').text('TOP SHOWS OF THE DAY')
            $('#all').css('text-decoration','none');
            $('#movies').css('text-decoration','none');
        break;
    } 
    RequeteTendances();
    window.scrollTo(0, 0);
    $(this).css('text-decoration','underline');
}

/*REQUETES AJAX*/
/*BOUTON QUI AFFICHE LES INFORMATIONS SUR LA SAISON DEMANDEE*/

function RequetedetailSaison(){
    var input ={
        'requeteType' : 'detailSaison',
        'id' : $('#idShow').val(),
        'idNumSeason' : $(this).attr('id'),
    }
    $.post('requetes.php',input,function(resultat){
        $('#detailSaison').html(resultat);
    },"html");
    window.location = URL('#detailSaison');
}

/* BOUTON QUI AFFICHE LA LISTES DE TOUS LES EPISODE DE LA SAISON SELECTIONNEE */

function RequetedetailEpisode(){
    var input ={
        'requeteType' : 'detailEpisode',
            'id' : $('#idShow').val(), 
            'idNumSeason' : $(this).attr('id'), 
      } 
    $.post('requetes.php',input,function(resultat){
        $('#episodesDetail').html(resultat);
    },"html");
}

/*AFFICHAGE DES DETAILS LORSQUE L'ON CLIQUE SUR L'AFFICHE */

function RequetepageDetail(){
    var input ={
        'requeteType' : 'pageDetail',
        'id' : $(this).attr('id'),
    }
    $.post('requetes.php',input,function(resultat){
        $('#corpsPage').html(resultat);
    },"html");
}

function RequeteTendances(){
    var input ={
        'requeteType' : 'AfficherTendences',
        'filtre' : filtre,
      } 

    $.post('requetes.php',input,function(resultat){
        $('#corpsPage').html(resultat);
    },"html");
    $('#detailSaison').text("");
    $('#detailSaison').html("");

}
/* RECHERCHES*/
function requeteSearch(){
    var inputSearch = $('#searchValue').val();  
    NewInputSearch = inputSearch.replaceAll(' ', '/');
    var input ={
        'requeteType' : 'pageAffiche',
        'inputSearch' : NewInputSearch,
        'filtre' : filtre,
    }
    $.post('requetes.php',input,function(resultat){
        /* Remplit la div miniature avec les affiches */
        $('#corpsPage').html(resultat);
    },"html");
    $('#detailSaison').text("");
    $('#titre').text(inputSearch.toUpperCase());
}

/*-------------------------------------------------------------------MAIN-------------------------------------------------------------------  */

$(document).ready(function(){
    var filtre = 'tv';

    $('#searchButton').click(requeteSearch);
    $(document).on('click','.AfficherTendences',afficherTendances);
    $(document).on('click','.wrapShow',RequetepageDetail);
    $(document).on('click','.buttonSeasonChose',RequetedetailSaison);
    $(document).on('click','.buttonEpisode',RequetedetailEpisode);

});









