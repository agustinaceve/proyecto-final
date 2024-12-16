// Manejo del formulario de inicio de sesión
const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const usuario = document.getElementById("usuario").value;
    const password = document.getElementById("password").value;

    try {
        const response = await fetch("http://localhost/proyectop/validar.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `usuario=${usuario}&password=${password}`,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === "success") {
            window.location.href = "inicio.html"; // Cambiar a la página de inicio
        } else {
            mostrarModal(result.message); // Mostrar el mensaje de error en el modal
        }
    } catch (error) {
        console.error("Error:", error);
        mostrarModal("Hubo un problema al procesar la solicitud.");
    }

    document.getElementById("cancelButton").addEventListener("click", () => {
        document.getElementById("loginForm").reset();
    });
});

// Mostrar el modal con un mensaje
function mostrarModal(mensaje) {
    const modal = document.getElementById("successModal");
    const successMessage = document.getElementById("successMessage");

    // Asignamos el mensaje dinámico
    successMessage.textContent = mensaje;

    // Mostramos el modal
    modal.classList.add("show");
}

// Cerrar el modal al hacer clic en el botón Aceptar
document.getElementById("successBtn").addEventListener("click", () => {
    const modal = document.getElementById("successModal");
    modal.classList.remove("show");
});
