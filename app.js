var nb_actuel;

function afficheProgramme() {
    var count = document.getElementById("programme_count").value;
    count != 0 ? nb_actuel = count : nb_actuel = 0;
    var programme;


    if (count != '')
        for (var i = 0; i < count; i++) {
            programme = document.getElementById("programme_" + i);   
            programme.style.display = "block";
        }
}

function showProgramme() {
    var programme = document.getElementById("programme_" + nb_actuel);
    programme.style.display = "block";

    if (nb_actuel < 9) {
        nb_actuel = Number(nb_actuel) + 1;
    }
}