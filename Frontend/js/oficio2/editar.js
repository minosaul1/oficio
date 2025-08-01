// Función para obtener parámetros GET de la URL
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

// Cargar datos al cargar la página
document.addEventListener('DOMContentLoaded', async () => {
  console.log('PASO 1: DOM cargado. El script de edición ha comenzado.');

  const id = getQueryParam('id');
  if (!id) {
    alert('ID de oficio no especificado');
    return;
  }

  try {
    const response = await fetch(`/oficio/Backend/oficio2/obtener_oficio.php?id=${id}`);
    const oficio = await response.json();
    
    console.log('PASO 2: Datos recibidos del servidor:', oficio);

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
    document.querySelector('input[name="fecha_recepcion"]').value = oficio.fecha_recepcion || '';
    document.querySelector('#paciente_nombre').value = oficio.paciente_nombre || '';

    // --- PRUEBA CLAVE PARA "FECHA_OFICIO" ---
    console.log('PASO 3: Intentando encontrar el campo de texto para la fecha...');
    const campoFechaOficio = document.querySelector('input[name="fecha_oficio"]');
    
    console.log('PASO 4: Elemento encontrado:', campoFechaOficio);

    if (campoFechaOficio) {
      console.log('PASO 5: El campo fue encontrado. Asignando el valor:', oficio.fecha_oficio);
      campoFechaOficio.value = oficio.fecha_oficio || '';
    } else {
      console.error('ERROR CRÍTICO: No se encontró el input con name="fecha_oficio". Revisa el HTML.');
    }
    // --- FIN DE PRUEBA ---

    if (oficio.fecha_creacion) {
  // 1. Crear un arreglo con los nombres de los meses.
  const meses = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
  ];

  const fecha = new Date(oficio.fecha_creacion);

  // 2. Obtener las partes de la fecha.
  const dia = fecha.getDate();
  const nombreMes = meses[fecha.getMonth()]; // Se obtiene "Julio" del arreglo.
  const anio = fecha.getFullYear();

  // 3. Unir todo en el formato deseado.
  document.getElementById('fecha').value = `${dia} de ${nombreMes} de ${anio}`;
}

  } catch (error) {
    alert('Error al cargar el oficio para editar');
    console.error('Error en el bloque try/catch:', error);
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
    const response = await fetch('/oficio/Backend/oficio2/actualizar_oficio.php', {
      method: 'POST',
      body: formData
    });
    const result = await response.json();
    if (result.success) {
      alert('Oficio actualizado correctamente');
    } else {
      alert('Error al actualizar el oficio');
    }
  } catch (error) {
    alert('Error al enviar datos');
    console.error('Error en el submit:', error);
  }
});