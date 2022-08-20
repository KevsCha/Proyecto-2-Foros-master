var editUser = document.getElementById('editUser');
editUser.onclick = function () {
    document.getElementById('userName').readOnly = false;
    document.getElementById('userName').value = "";
    document.getElementById('message').style.display = "none";
    document.getElementById('buttonContainer').style.display = "block";
}

var editPass = document.getElementById('editPass');
editPass.onclick = function () {
    document.getElementById("actualPass").value = "";
    document.getElementById('actualPass').readOnly = false;
    document.getElementById("passContainer").style.display = "block";
    document.getElementById('message').style.display = "none";
    document.getElementById('buttonContainer').style.display = "block";
}

document.getElementById('profilePic').onclick = function () {
    document.getElementById('buttonContainer').style.display = "block";
    document.getElementById('message').style.display = "none";

}

document.getElementById('cancelButton').onclick = function () {
    document.getElementById('userName').readOnly = true;
    document.getElementById('actualPass').readOnly = true;
    document.getElementById("passContainer").style.display = "none";
    document.getElementById('buttonContainer').style.display = "none";
}

document.getElementById('profilePic').onchange = function (e) {
    var reader = new FileReader();
    if (reader) {
        reader.readAsDataURL(e.target.files[0]);
        reader.onload = function () {
            var profilePic = document.getElementById('userProfilePic');
            profilePic.src = reader.result;
        };
    }
}

function edit(event) {
    openModal();
    var titulo = event.target.parentNode.parentNode.children[0].children[0].innerText;
    var description = event.target.parentNode.parentNode.children[0].children[1].innerHTML;
    var id = event.target.parentNode.parentNode.id;
    var category = event.target.parentNode.parentNode.children[0].children[2].innerText;

    var form = document.getElementById("postForm");
    var hidden = document.createElement("input");
    hidden.setAttribute("type", "hidden");
    hidden.setAttribute("name", "editPost");
    hidden.value = id;
    form.appendChild(hidden);
    document.getElementById("newPostInput").remove();
    document.getElementById("postTitle").value = titulo;
    tinymce.activeEditor.setContent(description)
    var select = document.getElementById("postCategory");
    select.setAttribute("disabled", "disabled");
    for (let option = 0; option < select.length; option++) {
        if (select.options[option].value == category) {
            select.options[option].selected = true;
        }
    }
    document.getElementById("publishButton").innerText = "Guardar";
}

function closePost(event) {
    var id = event.target.parentNode.parentNode.id;
    if (confirm("¿Quieres cerrar el hilo seleccionado?")) {
        event.target.value = id;
        var form = event.target.parentNode;
        form.submit();
    }
}

function deleteProfile(event) {
    if (confirm("¿Quieres eliminar para siempre tu cuenta?")) {
        var form = event.target.parentNode;
        var hidden = document.createElement("input");
        hidden.setAttribute("type", "hidden");
        hidden.setAttribute("name", "deleteProfile");
        form.appendChild(hidden);
        form.submit();
    }

}