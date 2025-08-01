 // Función para obtener parámetros GET de la URL
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  // Cargar datos al cargar la página
  document.addEventListener('DOMContentLoaded', async () => {
    const id = getQueryParam('id');
    if (!id) {
      alert('ID de oficio no especificado');
      return;
    }

    try {
      // Cambiar a la ruta correcta de tu archivo obtener_oficio.php
      const response = await fetch(`/oficio/Backend/oficio/obtener_oficio.php?id=${id}`);

      const oficio = await response.json();

      if (!oficio) {
        alert('Oficio no encontrado');
        return;
      }

      // Llenar los campos del formulario
      document.querySelector('input[name="oficio_no"]').value = oficio.oficio_no || '';
      document.getElementById('oficio-no-texto').textContent = oficio.oficio_no || '';
      document.querySelector('input[name="nombre_remitente"]').value = oficio.nombre_remitente || '';
      document.querySelector('input[name="cargo_remitente"]').value = oficio.cargo_remitente || '';
      document.querySelector('input[name="area_remitente"]').value = oficio.area_remitente || '';
      document.querySelector('input[name="oficio_ref"]').value = oficio.oficio_ref || '';
      document.querySelector('input[name="cdi"]').value = oficio.cdi || '';
      document.querySelector('input[name="fecha_oficio"]').value = oficio.fecha_oficio || '';
      document.querySelector('input[name="fecha_recepcion"]').value = oficio.fecha_recepcion || '';
      document.querySelector('#paciente_nombre').value = oficio.paciente_nombre || '';

      if (oficio.fecha_registro) {
        // 1. Definir el arreglo de meses en español.
        const meses = [
          "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
          "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        const fecha = new Date(oficio.fecha_registro);

        // 2. Obtener las partes de la fecha usando el arreglo.
        const dia = fecha.getDate();
        const nombreMes = meses[fecha.getMonth()];
        const anio = fecha.getFullYear();

        // 3. Asignar el valor con el nuevo formato.
        document.getElementById('fecha').value = `${dia} de ${nombreMes} de ${anio}`;
      }

    } catch (error) {
      alert('Error al cargar el oficio para editar');
      console.error(error);
    }
  });

   document.getElementById('form-oficio').addEventListener('submit', async function (e) {
    e.preventDefault(); // Evita recarga

    const id = getQueryParam('id');
    if (!id) {
      alert('ID de oficio no especificado');
      return;
    }

    const formData = new FormData(this);
    formData.append('id', id); // Agrega el id al FormData

    try {
      const response = await fetch('/oficio/Backend/oficio/actualizar_oficio.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert('Oficio actualizado correctamente');
        // Redireccionar o mostrar mensaje
      } else {
        alert('Error al actualizar el oficio');
      }
    } catch (error) {
      alert('Error al enviar datos');
      console.error(error);
    }
  });