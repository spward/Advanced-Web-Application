window.pressed = function () {
    var a = document.getElementById('fileToUpload');
    if (a.value == "") {
        fileLabel.innerHTML = "Choose file";
    }
    else {
        var theSplit = a.value.split('\\');
        fileLabel.innerHTML = theSplit[theSplit.length - 1];
    }
};
