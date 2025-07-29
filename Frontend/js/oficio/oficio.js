 // ✅ Establecer la fecha actual en el campo de fecha con formato "dd/mm/yyyy"
  const fechaInput = document.getElementById('fecha');
  const hoy = new Date();
  const dia = String(hoy.getDate()).padStart(2, '0');
  const anio = hoy.getFullYear();
  const meses = [
    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
  ];
  const mesTexto = meses[hoy.getMonth()];
  const fechaFormateada = `${dia} de ${mesTexto} de ${anio}`;
  document.getElementById('fecha').value = fechaFormateada;


  // ✅ Manejar el envío del formulario con AJAX
  document.getElementById("form-oficio").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // (Opcional) Ver datos enviados en consola para debug
    // for (let [key, value] of formData.entries()) {
    //   console.log(key + ': ' + value);
    // }

    fetch("/oficio/Backend/oficio/guardar_oficio.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(respuesta => {
      if (respuesta.success) {
        // Mostrar número de oficio generado
        document.getElementById("oficio-no-texto").textContent = respuesta.oficio_no;
        form.querySelector('input[name="oficio_no"]').value = respuesta.oficio_no;

        // Imprimir inmediatamente
        window.print();
      } else {
        alert("Error al guardar el oficio: " + (respuesta.error || "Error desconocido"));
      }
    })

    .catch(error => {
      alert("Error de conexión con el servidor.");
      console.error(error);
    });
  });