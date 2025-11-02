function openEditModal(id, login, rol) {
  document.getElementById('editID').value = id;
  document.getElementById('editLogin').value = login;

  const rolMap = {
    'Administrador': 1,
    'Supervisor': 2,
    'Técnico': 3,
    'Usuario': 4
  };
  document.getElementById('editRol').value = rolMap[rol] || 4;

  document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('editModal').style.display = 'none';
}

function viewUser(id) {
  alert("Aquí podrías abrir un modal con más detalles del usuario ID: " + id);
}

function deleteUser(id) {
  if (confirm("¿Seguro que quieres eliminar este usuario?")) {
    window.location.href = "../controllers/delete_user.php?id=" + id;
  }
}
