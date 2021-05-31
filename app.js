var nb = 0;

function initElement()
{
    var _showProgramme = document.getElementById("showProgramme");
    _showProgramme.onclick = showProgramme;
};

function showProgramme() {
    var programme = document.getElementById("programme_" + nb);
    console.log('programme_' + nb);
    programme.style.display = "block";
    nb += 1;
}