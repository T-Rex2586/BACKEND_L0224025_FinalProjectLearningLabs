function enableEditForm(id, currentText) {
    document.getElementById('editForm' + id).style.display = 'block';
    document.getElementById('taskText' + id).value = currentText;
}

function disableEditForm(id) {
    document.getElementById('editForm' + id).style.display = 'none';
}