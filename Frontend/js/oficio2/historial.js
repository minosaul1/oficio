    // Variables globales
    let oficios = [];
    let paginaActual = 1;
    const oficiosPorPagina = 20;
    let oficioAEliminar = null;

    // Función para cargar los oficios desde la API
    async function cargarOficios() {
      try {
        // Aquí debes reemplazar con tu endpoint real
        const response = await fetch('/oficio/Backend/oficio2/obtener_datos.php');
        if (!response.ok) {
          throw new Error('Error al cargar los oficios');
        }
        oficios = await response.json();
        cargarTabla();
      } catch (error) {
        console.error('Error:', error);
        mostrarMensajeError('Error al cargar los oficios');
      }
    }

    function cargarTabla(data = oficios) {
      const tbody = document.getElementById('oficiosTableBody');
      tbody.innerHTML = '';

      const inicio = (paginaActual - 1) * oficiosPorPagina;
      const fin = inicio + oficiosPorPagina;
      const oficiosPagina = data.slice(inicio, fin);

      if (oficiosPagina.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="7" style="text-align: center; padding: 20px;">
              No se encontraron oficios registrados
            </td>
          </tr>
        `;
        return;
      }

      oficiosPagina.forEach(oficio => {
        // Formatear fecha de registro
        const fechaRegistro = new Date(oficio.fecha_registro);
        const fechaFormateada = fechaRegistro.toLocaleDateString('es-MX') + ' ' + 
                             fechaRegistro.toLocaleTimeString('es-MX', {hour: '2-digit', minute: '2-digit'});

        const fila = `
          <tr>
            <td><strong>${oficio.oficio_no}</strong></td>
            <td>${fechaFormateada}</td>
            <td>
              <strong>${oficio.nombre_remitente}</strong><br>
              <small style="color: #666;">${oficio.cargo_remitente}</small>
            </td>
            <td>${oficio.cdi}</td>
            <td>${oficio.fecha_oficio}</td>
            <td>${oficio.paciente_nombre}</td>
            <td>
              <div class="actions">
                <button class="btn btn-editar" onclick="editarOficio(${oficio.id})" title="Editar">
                  ✏️
                </button>
              </div>
            </td>
          </tr>
        `;
        tbody.innerHTML += fila;
      });

      actualizarPaginacion(data.length);
    }

    function actualizarPaginacion(totalOficios) {
      const totalPaginas = Math.ceil(totalOficios / oficiosPorPagina);
      document.getElementById('pageInfo').textContent = `Página ${paginaActual} de ${totalPaginas || 1}`;
    }

    function cambiarPagina(direccion) {
      const totalPaginas = Math.ceil(oficios.length / oficiosPorPagina);
      const nuevaPagina = paginaActual + direccion;
      
      if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
        paginaActual = nuevaPagina;
        cargarTabla();
      }
    }

    function buscarOficios() {
      const termino = document.getElementById('searchInput').value.toLowerCase();
      const oficiosFiltrados = oficios.filter(oficio => 
        (oficio.oficio_no && oficio.oficio_no.toLowerCase().includes(termino)) ||
        (oficio.nombre_remitente && oficio.nombre_remitente.toLowerCase().includes(termino)) ||
        (oficio.cdi && oficio.cdi.toString().toLowerCase().includes(termino)) ||
        (oficio.paciente_nombre && oficio.paciente_nombre.toLowerCase().includes(termino))
      );
      
      paginaActual = 1;
      cargarTabla(oficiosFiltrados);
    }

    async function editarOficio(id) {
      try {
        // Aquí implementarías la lógica para obtener los datos del oficio
        // const response = await fetch(`tu_api_endpoint/aqui/${id}`);
        // const oficio = await response.json();
        
        // Ejemplo de redirección a página de edición
        window.location.href = `/oficio/Frontend/web/oficio2/editar.html?id=${id}`;
      } catch (error) {
        console.error('Error al editar oficio:', error);
        mostrarMensajeError('Error al cargar el oficio para editar');
      }
    }

    function eliminarOficio(id) {
      oficioAEliminar = id;
      document.getElementById('confirmModal').style.display = 'block';
    }

    async function confirmarEliminacion() {
      if (!oficioAEliminar) return;
      
      try {
        // Aquí implementarías la llamada a la API para eliminar
        // const response = await fetch(`tu_api_endpoint/aqui/${oficioAEliminar}`, {
        //   method: 'DELETE'
        // });
        
        // if (!response.ok) {
        //   throw new Error('Error al eliminar el oficio');
        // }
        
        // Actualizar la lista después de eliminar
        await cargarOficios();
        cerrarModal();
        mostrarMensajeExito('Oficio eliminado correctamente');
      } catch (error) {
        console.error('Error al eliminar oficio:', error);
        mostrarMensajeError('Error al eliminar el oficio');
      }
    }

    function cerrarModal() {
      document.getElementById('confirmModal').style.display = 'none';
      oficioAEliminar = null;
    }

    function imprimirOficio(id) {
      // Ejemplo de redirección a página de impresión
      window.open(`/imprimir-oficio.html?id=${id}`, '_blank');
    }

    function nuevoOficio() {
      // Redirección a página de creación
      window.location.href = '/nuevo-oficio.html';
    }

    function mostrarMensajeExito(mensaje) {
      const mensajeElemento = document.createElement('div');
      mensajeElemento.innerHTML = `✅ ${mensaje}`;
      mensajeElemento.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 1001;
        background: #27ae60; color: white; padding: 15px 25px;
        border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease;
      `;
      document.body.appendChild(mensajeElemento);
      
      setTimeout(() => {
        document.body.removeChild(mensajeElemento);
      }, 3000);
    }

    function mostrarMensajeError(mensaje) {
      const mensajeElemento = document.createElement('div');
      mensajeElemento.innerHTML = `❌ ${mensaje}`;
      mensajeElemento.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 1001;
        background: #e74c3c; color: white; padding: 15px 25px;
        border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease;
      `;
      document.body.appendChild(mensajeElemento);
      
      setTimeout(() => {
        document.body.removeChild(mensajeElemento);
      }, 3000);
    }

    // Event Listeners
    document.getElementById('searchInput').addEventListener('input', buscarOficios);

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
      const modal = document.getElementById('confirmModal');
      if (event.target === modal) {
        cerrarModal();
      }
    }

    // Cargar tabla al inicializar
    document.addEventListener('DOMContentLoaded', function() {
      cargarOficios();
    });